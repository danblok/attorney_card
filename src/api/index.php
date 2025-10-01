<?php

/**
 * API для работы Доверенностей
 * Единый интерфейс для всех операций
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Crm\AddressTable;
use Bitrix\Crm\CompanyTable;
use Bitrix\Crm\EntityAddressType;
use Bitrix\Crm\RequisiteTable;
use Bitrix\Crm\Service\Container;
use Bitrix\Main\Application;
use Bitrix\Main\DB\SqlExpression;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Loader;
use Bitrix\Main\UserFieldTable;
use Bitrix\Main\Web\Json;

class API
{
    const HEAD_DEPARTMENT_ID = 1;

    private $request;

    private $method;

    private $endpoint;

    public function __construct()
    {
        // Загружаем необходимые модули
        if (! Loader::includeModule('crm')) {
            $this->sendError('CRM модуль не установлен');
        }

        $this->request = Application::getInstance()->getContext()->getRequest();
        $this->method = $this->request->getRequestMethod();

        // Получаем путь после /ac/api/
        $requestUri = $_SERVER['REQUEST_URI'];
        $queryString = $_SERVER['QUERY_STRING'] ?? '';

        // Убираем GET параметры из URI
        if (! empty($queryString)) {
            $requestUri = str_replace('?'.$queryString, '', $requestUri);
        }

        $apiBasePath = '/ac/api/';
        $apiPosition = strpos($requestUri, $apiBasePath);

        if ($apiPosition !== false) {
            $this->endpoint = substr($requestUri, $apiPosition + strlen($apiBasePath));
            $this->endpoint = trim($this->endpoint, '/');
        } else {
            // Если вызов идет напрямую к index.php
            $this->endpoint = $_GET['endpoint'] ?? '';
        }

        // Устанавливаем заголовки для JSON ответа
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // Обрабатываем preflight запросы
        if ($this->method === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    /**
     * Основной метод для маршрутизации запросов
     */
    public function handleRequest()
    {
        try {
            // Парсим URL для определения ресурса и действия
            $pathParts = explode('/', trim($this->endpoint, '/'));
            $resource = $pathParts[0] ?? '';
            $action = $pathParts[1] ?? '';

            switch ($resource) {
                case 'authorities':
                    $this->handleAuthorities($action);
                case 'representatives':
                    $this->handleRepresentatives($action);
                case 'contacts':
                    $this->handleContacts($action);
                case 'companies':
                    $this->handleCompanies($action);
                case 'templates':
                    $this->handleTemplates($action);
                case 'forms':
                    $this->handleForms($action);
                case 'subjects':
                    $this->handleSubjects($action);
                case 'systems':
                    $this->handleSystems($action);
                case 'statuses':
                    $this->handleStatuses($action);
                case 'users':
                    $this->handleUsers($action);
                case 'areas':
                    $this->handleActivityAreas($action);
                case 'file':
                    $this->handleFile($action);
                case 'project':
                    $this->handleProject($action);
                default:
                    $this->sendError('Неизвестный ресурс: '.$resource, 404);
            }

        } catch (Exception $e) {
            $this->sendError('Внутренняя ошибка сервера: '.$e->getMessage(), 500);
        }
    }

    /**
     * Обработка запросов к полномочиям
     */
    private function handleAuthorities($action = '')
    {
        switch ($this->method) {
            case 'GET':
                if ($action === 'list' || $action === '') {
                    $this->getAuthoritiesList();
                }
            case 'POST':
            case 'PATCH':
            case 'PUT':
            case 'DELETE':
            default:
                $this->sendError('Метод не поддерживается', 405);
        }
    }

    /**
     * Получение списка полномочий из смарт-процесса "Справочник полномочий"
     * EntityId = 1108
     * Возвращает массив объектов [{"label": UF_CRM_20_1749034302, "value": ID}]
     */
    private function getAuthoritiesList()
    {
        try {
            $entityId = 1108;

            // Получаем фабрику для работы со смарт-процессами
            $factory = Container::getInstance()->getFactory($entityId);

            if (! $factory) {
                $this->sendError('Смарт-процесс с ID '.$entityId.' не найден', 404);
            }

            // Создаем объект для выборки данных
            $dataClass = $factory->getDataClass();

            $result = $dataClass::getList([
                'select' => [
                    'ID',
                    'UF_CRM_20_1749034302', // Пользовательское поле
                ],
                'filter' => [], // Можно добавить фильтры при необходимости
                'order' => ['ID' => 'ASC'],
            ]);

            $authorities = [];

            while ($item = $result->fetch()) {
                $authorities[] = [
                    'value' => $item['UF_CRM_20_1749034302'] ?? '',
                    'id' => (string) $item['ID'],
                ];
            }

            $this->sendSuccess($authorities);

        } catch (Exception $e) {
            $this->sendError('Ошибка при получении списка полномочий: '.$e->getMessage());
        }
    }

    /**
     * Обработка запросов к представителям
     */
    private function handleRepresentatives($action = '')
    {
        switch ($this->method) {
            case 'GET':
                if ($action === 'list' || $action === '') {
                    $this->getRepresentativesList();
                }

            case 'POST':
            case 'PATCH':
            case 'PUT':
            case 'DELETE':
            default:
                $this->sendError('Метод не поддерживается', 405);
        }
    }

    /**
     * Получение списка представителей по доверенности
     * EntityId = 1124
     * Логика фильтрации:
     * - Если UF_CRM_24_1750660700 = false и нет элемента смарт-процесса с UF_CRM_24_1750933837 = ID контакта - берем данные контакта
     * - Если UF_CRM_24_1750660700 = true и нет элемента смарт-процесса с UF_CRM_24_1750933837 = ID пользователя - берем данные пользователя
     * - Иначе берем данные элемента смарт-процесса
     */
    private function getRepresentativesList()
    {
        try {
            if (! Loader::includeModule('main')) {
                $this->sendError('Main модуль не установлен');
            }

            $entityId = 1124;

            // Получаем фабрику для работы со смарт-процессами
            $factory = Container::getInstance()->getFactory($entityId);

            if (! $factory) {
                $this->sendError('Смарт-процесс с ID '.$entityId.' не найден', 404);
            }

            // Получаем все элементы смарт-процесса "Представители по доверенности"
            $dataClass = $factory->getDataClass();

            $smartProcessResult = $dataClass::getList([
                'select' => [
                    'ID',
                    'UF_CRM_24_1750933837', // externalId - привязка к контакту или пользователю
                    'UF_CRM_24_1750660700', // isGardiaEmployee
                    'UF_CRM_24_1750933778', // firstName
                    'UF_CRM_24_1750933757', // lastName
                    'UF_CRM_24_1750933794', // secondName
                    'UF_CRM_24_1750933810', // position
                    'UF_CRM_24_1750660895', // passportIssuedBy
                    'UF_CRM_24_1750660936', // passportIssuedDate
                    'UF_CRM_24_1750660992', // divisionCode
                    'UF_CRM_24_1750661114', // registrationAddress
                    'UF_CRM_24_1750739633', // additionalInfo
                    'UF_CRM_24_1750660794', // passportSeries
                    'UF_CRM_24_1750660845',  // passportNumber
                ],
                'order' => ['ID' => 'ASC'],
            ]);

            $smartProcessItems = [];
            $existingReprIds = [];

            while ($item = $smartProcessResult->fetch()) {
                $smartProcessItems[] = $item;
                $existingReprIds[] = (string) $item['UF_CRM_24_1750933837'];
            }

            // Получаем все контакты
            $contacts = $this->getAllContacts();

            // Получаем всех пользователей
            $users = $this->getAllUsers();

            $result = [];

            // Обрабатываем элементы смарт-процесса
            foreach ($smartProcessItems as $item) {
                $result[] = $this->formatRepresentative($item);
            }

            // Обрабатываем контакты (UF_CRM_24_1750660700 = false)
            foreach ($contacts as $contact) {
                $contactId = (string) $contact['ID'];

                // Если нет элемента смарт-процесса с таким externalId
                if (! in_array($contactId, $existingReprIds)) {
                    $result[] = $this->formatContactToRepresentative($contact);
                }
            }

            // Обрабатываем пользователей (UF_CRM_24_1750660700 = true)
            foreach ($users as $user) {
                $userId = (string) $user['ID'];

                // Если нет элемента смарт-процесса с таким externalId
                if (! in_array($userId, $existingReprIds)) {
                    $result[] = $this->formatUserToRepresentative($user);
                }
            }

            $this->sendSuccess($result);

        } catch (Exception $e) {
            $this->sendError('Ошибка при получении списка представителей: '.$e->getMessage());
        }
    }

    /*
    * Возвращает представителя по доверенности по ID
    */
    private function getRepresentativeById(int $id)
    {
        try {
            if (! Loader::includeModule('main')) {
                $this->sendError('Main модуль не установлен');
            }

            $entityId = 1124;

            // Получаем фабрику для работы со смарт-процессами
            $factory = Container::getInstance()->getFactory($entityId);

            if (! $factory) {
                $this->sendError('Смарт-процесс с ID '.$entityId.' не найден', 404);
            }

            // Получаем все элементы смарт-процесса "Представители по доверенности"
            $dataClass = $factory->getDataClass();

            $res = $dataClass::getList([
                'select' => [
                    'ID',
                    'UF_CRM_24_1750933837', // externalId - привязка к контакту или пользователю
                    'UF_CRM_24_1750660700', // isGardiaEmployee
                    'UF_CRM_24_1750933778', // firstName
                    'UF_CRM_24_1750933757', // lastName
                    'UF_CRM_24_1750933794', // secondName
                    'UF_CRM_24_1750933810', // position
                    'UF_CRM_24_1750660895', // passportIssuedBy
                    'UF_CRM_24_1750660936', // passportIssuedDate
                    'UF_CRM_24_1750660992', // divisionCode
                    'UF_CRM_24_1750661114', // registrationAddress
                    'UF_CRM_24_1750739633', // additionalInfo
                    'UF_CRM_24_1750660794', // passportSeries
                    'UF_CRM_24_1750660845',  // passportNumber
                ],
                'filter' => [
                    'ID' => $id,
                ],
                'order' => ['ID' => 'ASC'],
            ]);

            return $this->formatRepresentative($res->fetch());

        } catch (Exception $e) {
            $this->sendError('Ошибка при получении списка представителей: '.$e->getMessage());
        }

    }

    /**
     * Обработка запросов к представителям
     */
    private function handleContacts($action = '')
    {
        switch ($this->method) {
            case 'GET':
                if ($action === 'list' || $action === '') {
                    $this->getContactsList();
                }
            case 'POST':
            case 'PATCH':
            case 'PUT':
            case 'DELETE':
            default:
                $this->sendError('Метод не поддерживается', 405);
        }
    }

    /**
     * Получение списка контактов
     * Формат: [{ id, firstName, lastName, secondName, position }]
     */
    private function getContactsList()
    {
        try {
            $contacts = $this->getAllContacts();

            $contacts = array_map(fn ($contact) => [
                'id' => (string) $contact['ID'],
                'firstName' => (string) ($contact['NAME'] ?? ''),
                'lastName' => (string) ($contact['LAST_NAME'] ?? ''),
                'secondName' => (string) ($contact['SECOND_NAME'] ?? ''),
                'position' => (string) ($contact['POST'] ?? ''),
            ], $contacts);

            $this->sendSuccess($contacts);
        } catch (Exception $e) {
            $this->sendError('Ошибка при получении списка контактов: '.$e->getMessage());
        }

    }

    /**
     * Получение всех контактов
     */
    private function getAllContacts()
    {
        $contacts = [];

        $contactResult = \CCrmContact::GetList(
            ['ID' => 'ASC'],
            ['CHECK_PERMISSIONS' => 'N'],
            [
                'ID',
                'NAME',
                'LAST_NAME',
                'SECOND_NAME',
                'POST',
                // Добавить другие поля контакта при необходимости
            ]
        );

        while ($contact = $contactResult->Fetch()) {
            $contacts[] = $contact;
        }

        return $contacts;
    }

    /**
     * Получение контакта по ID
     */
    private function getContactById(int $id)
    {
        $contactResult = \CCrmContact::GetList(
            ['ID' => 'ASC'],
            ['CHECK_PERMISSIONS' => 'N', 'ID' => $id],
            [
                'ID',
                'NAME',
                'LAST_NAME',
                'SECOND_NAME',
                'POST',
            ]
        );

        return $contactResult->fetch();
    }

    /**
     * Получение списка пользователей
     * Формат: [{ id, firstName, lastName, secondName, position }]
     */
    private function getUsersList()
    {
        try {
            $users = $this->getAllUsers();

            $users = array_map(fn ($user) => [
                'id' => (string) $user['ID'],
                'firstName' => (string) ($user['NAME'] ?? ''),
                'lastName' => (string) ($user['LAST_NAME'] ?? ''),
                'secondName' => (string) ($user['SECOND_NAME'] ?? ''),
                'position' => (string) ($user['WORK_POSITION'] ?? ''),
            ], $users);

            $this->sendSuccess($users);
        } catch (Exception $e) {
            $this->sendError('Ошибка при получении списка контактов: '.$e->getMessage());
        }

    }

    /**
     * Получение всех пользователей
     */
    private function getAllUsers()
    {
        $users = [];

        $userResult = \CUser::GetList(
            'ID',
            'ASC',
            ['ACTIVE' => 'Y'],
            [
                'SELECT' => [
                    'ID',
                    'NAME',
                    'LAST_NAME',
                    'SECOND_NAME',
                    'WORK_POSITION',
                ],
            ]
        );

        while ($user = $userResult->Fetch()) {
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Получение пользователя по ID
     */
    private function getUserById(int $id)
    {
        $userResult = \CUser::GetList(
            'ID',
            'ASC',
            ['ACTIVE' => 'Y', 'ID' => $id],
            [
                'SELECT' => [
                    'ID',
                    'NAME',
                    'LAST_NAME',
                    'SECOND_NAME',
                    'WORK_POSITION',
                ],
            ]
        );

        return $userResult->fetch();
    }

    /**
     * Обработка запросов к текущему пользователю
     */
    private function handleUsers($action = '')
    {
        switch ($this->method) {
            case 'GET':

                if ($action === 'list' || $action === '') {
                    $this->getUsersList();
                } elseif ($action === 'current') {
                    $this->getCurrentUser();
                } elseif ($action === 'director') {
                    $this->getDirector();
                }
            case 'POST':
            case 'PATCH':
            case 'PUT':
            case 'DELETE':
            default:
                $this->sendError('Метод не поддерживается', 405);
        }
    }

    /**
     * Получение текущего пользователя
     */
    private function getCurrentUser()
    {

        try {
            global $USER;
            $currentUserId = $USER->GetID();
            $res = \CUser::GetList(
                'ID',
                'ASC',
                ['ACTIVE' => 'Y', 'ID' => $currentUserId],
                [
                    'SELECT' => [
                        'ID',
                        'NAME',
                        'LAST_NAME',
                        'SECOND_NAME',
                        'WORK_POSITION',
                    ],
                ]
            );

            $user = $res->Fetch();
            $this->sendSuccess([
                'id' => $user['ID'],
                'firstName' => $user['NAME'],
                'lastName' => $user['LAST_NAME'],
                'secondName' => $user['SECOND_NAME'],
                'position' => $user['WORK_POSITION'],
            ]);

        } catch (\Exception $error) {
            $this->sendError($user);
        }
    }

    /**
     * Форматирование представителя из элемента смарт-процесса
     */
    private function formatRepresentative($item)
    {
        return [
            'id' => (string) $item['ID'],
            'externalId' => (string) ($item['UF_CRM_24_1750933837'] ?? 0),
            'isGardiaEmployee' => (bool) ($item['UF_CRM_24_1750660700'] ?? false),
            'firstName' => (string) ($item['UF_CRM_24_1750933778'] ?? ''),
            'lastName' => (string) ($item['UF_CRM_24_1750933757'] ?? ''),
            'secondName' => (string) ($item['UF_CRM_24_1750933794'] ?? ''),
            'position' => (string) ($item['UF_CRM_24_1750933810'] ?? ''),
            'passportIssuedBy' => (string) ($item['UF_CRM_24_1750660895'] ?? ''),
            'passportIssuedDate' => (string) ($item['UF_CRM_24_1750660936'] ?? ''),
            'divisionCode' => (string) ($item['UF_CRM_24_1750660992'] ?? ''),
            'registrationAddress' => (string) ($item['UF_CRM_24_1750661114'] ?? ''),
            'additionalInfo' => (string) ($item['UF_CRM_24_1750739633'] ?? ''),
            'passportSeries' => (string) ($item['UF_CRM_24_1750660794'] ?? ''),
            'passportNumber' => (string) ($item['UF_CRM_24_1750660845'] ?? ''),
        ];
    }

    /**
     * Форматирование представителя из контакта
     */
    private function formatContactToRepresentative($contact)
    {
        return [
            'id' => $this->generateRandomString(), // У контакта нет ID в смарт-процессе, поэтому генерируем рандомный ID
            'externalId' => (string) $contact['ID'],
            'isGardiaEmployee' => false, // Контакты = false
            'firstName' => (string) ($contact['NAME'] ?? ''),
            'lastName' => (string) ($contact['LAST_NAME'] ?? ''),
            'secondName' => (string) ($contact['SECOND_NAME'] ?? ''),
            'position' => (string) ($contact['POST'] ?? ''),
            'passportIssuedBy' => '',
            'passportIssuedDate' => '',
            'divisionCode' => '',
            'registrationAddress' => '',
            'additionalInfo' => '',
            'passportSeries' => '',
            'passportNumber' => '',
        ];
    }

    /**
     * Форматирование представителя из пользователя
     */
    private function formatUserToRepresentative($user)
    {
        return [
            'id' => $this->generateRandomString(), // У пользователя нет ID в смарт-процессе, поэтому генерируем рандомный ID
            'externalId' => (string) $user['ID'],
            'isGardiaEmployee' => true, // Пользователи = true
            'firstName' => (string) ($user['NAME'] ?? ''),
            'lastName' => (string) ($user['LAST_NAME'] ?? ''),
            'secondName' => (string) ($user['SECOND_NAME'] ?? ''),
            'position' => (string) ($user['WORK_POSITION'] ?? ''),
            'passportIssuedBy' => '',
            'passportIssuedDate' => '',
            'divisionCode' => '',
            'registrationAddress' => '',
            'additionalInfo' => '',
            'passportSeries' => '',
            'passportNumber' => '',
        ];
    }

    /**
     * Обработка запросов к компаниям
     */
    private function handleCompanies($action = '')
    {
        switch ($this->method) {
            case 'GET':
                if ($action === 'list' || $action === '') {
                    $this->getCompaniesList();
                }
            case 'POST':
            case 'PATCH':
            case 'PUT':
            case 'DELETE':
            default:
                $this->sendError('Метод не поддерживается', 405);
        }
    }

    private function getCompaniesList()
    {
        try {
            $companies = [];

            $companyResult = CompanyTable::getList([
                'select' => [
                    'ID',
                    'TITLE',
                    'RQ_ID' => 'REQUISITE.ID',
                    'NAME' => 'REQUISITE.RQ_COMPANY_NAME',
                    'FULL_NAME' => 'REQUISITE.RQ_COMPANY_FULL_NAME',
                    'INN' => 'REQUISITE.RQ_INN',
                    'OGRN' => 'REQUISITE.RQ_OGRN',
                    'ADDR' => 'REQ_ADDRESS.ADDRESS_2',
                ],
                'runtime' => [
                    new ReferenceField(
                        'REQUISITE',
                        RequisiteTable::class,
                        [
                            '=this.ID' => 'ref.ENTITY_ID',
                            'ref.ENTITY_TYPE_ID' => new SqlExpression('?i', \CCrmOwnerType::Company),
                        ],
                        ['join_type' => 'left']
                    ),
                    new ReferenceField(
                        'REQ_ADDRESS',
                        AddressTable::class,
                        [
                            '=ref.ENTITY_ID' => 'this.REQUISITE.ID',
                            'ref.ANCHOR_ID' => 'this.ID',
                            'ref.TYPE_ID' => new SqlExpression('?i', EntityAddressType::Registered), // юр. адрес
                        ],
                        ['join_type' => 'left']
                    ),
                ],
                'order' => ['ID' => 'ASC'],
            ]);

            while ($company = $companyResult->Fetch()) {
                $companies[] = $this->formatCompany($company);
            }

            $this->sendSuccess($companies);

        } catch (Exception $e) {
            $this->sendError('Ошибка при получении списка компаний: '.$e->getMessage());
        }
    }

    private function getCompanyById(int $id)
    {
        try {
            $companyResult = CompanyTable::getList([
                'select' => [
                    'ID',
                    'TITLE',
                    'RQ_ID' => 'REQUISITE.ID',
                    'NAME' => 'REQUISITE.RQ_COMPANY_NAME',
                    'FULL_NAME' => 'REQUISITE.RQ_COMPANY_FULL_NAME',
                    'INN' => 'REQUISITE.RQ_INN',
                    'OGRN' => 'REQUISITE.RQ_OGRN',
                    'ADDR' => 'REQ_ADDRESS.ADDRESS_2',
                ],
                'runtime' => [
                    new ReferenceField(
                        'REQUISITE',
                        RequisiteTable::class,
                        [
                            '=this.ID' => 'ref.ENTITY_ID',
                            'ref.ENTITY_TYPE_ID' => new SqlExpression('?i', \CCrmOwnerType::Company),
                        ],
                        ['join_type' => 'left']
                    ),
                    new ReferenceField(
                        'REQ_ADDRESS',
                        AddressTable::class,
                        [
                            '=ref.ENTITY_ID' => 'this.REQUISITE.ID',
                            'ref.ANCHOR_ID' => 'this.ID',
                            'ref.TYPE_ID' => new SqlExpression('?i', EntityAddressType::Registered), // юр. адрес
                        ],
                        ['join_type' => 'left']
                    ),
                ],
                'filter' => ['ID' => $id],
                'order' => ['ID' => 'ASC'],
            ]);

            return $companyResult->fetch();

        } catch (Exception $e) {
            throw new Error("Не удалось получить компанию с ID #$id: ".$e->getMessage());
        }
    }

    /*
    *  Форматирует результат запроса из БД для API ответа
    */
    private function formatCompany(array $company)
    {
        return [
            'id' => (string) $company['ID'],
            'fullName' => (string) ($company['FULL_NAME'] ?? ''),
            'shortName' => (string) ($company['NAME'] ?? $company['TITLE'] ?? ''),
            'ogrn' => (string) ($company['OGRN'] ?? ''),
            'inn' => (string) ($company['INN'] ?? ''),
            'address' => (string) ($company['ADDR'] ?? ''),

        ];
    }

    /*
    *  Форматирует результат запроса пользователя для API
    */
    private function formatUser(array $user)
    {
        return [
            'id' => $user['ID'],
            'firstName' => $user['NAME'],
            'lastName' => $user['LAST_NAME'],
            'secondName' => $user['SECOND_NAME'],
            'position' => $user['WORK_POSITION'],
        ];
    }

    /*
    *  Форматирует результат запроса контакта для API
    */
    private function formatContact(array $user)
    {
        return [
            'id' => $user['ID'],
            'firstName' => $user['NAME'],
            'lastName' => $user['LAST_NAME'],
            'secondName' => $user['SECOND_NAME'],
            'position' => $user['POST'],
        ];
    }

    /**
     * Обработка запросов к шаблонам доверенностей
     */
    private function handleTemplates($action = '')
    {
        switch ($this->method) {
            case 'GET':
                if ($action === 'list' || $action === '') {
                    $this->getPowerOfAttorneyTemplatesList();
                }
            default:
                $this->sendError('Метод не поддерживается', 405);
        }
    }

    /**
     * Получение списка шаблонов доверенностей
     * Поле UF_CRM_21_1750336735 из смарт-процесса entityId = 1112
     */
    private function getPowerOfAttorneyTemplatesList()
    {
        $this->getListFieldValues('UF_CRM_21_1750336735');
    }

    /**
     * Обработка запросов к системам доверенностей
     */
    private function handleSystems($action = '')
    {
        switch ($this->method) {
            case 'GET':
                if ($action === 'list' || $action === '') {
                    $this->getPowerOfAttorneySystemsList();
                }
            default:
                $this->sendError('Метод не поддерживается', 405);
        }
    }

    /**
     * Получение списка систем выпуска доверенности
     * Поле UF_CRM_21_1750336927 из смарт-процесса entityId = 1112
     */
    private function getPowerOfAttorneySystemsList()
    {
        $this->getListFieldValues('UF_CRM_21_1750336927');
    }

    /**
     * Обработка запросов к формам доверенностей
     */
    private function handleForms($action = '')
    {
        switch ($this->method) {
            case 'GET':
                if ($action === 'list' || $action === '') {
                    $this->getPowerOfAttorneyFormsList();
                }
            default:
                $this->sendError('Метод не поддерживается', 405);
        }
    }

    /**
     * Получение списка форм выпуска доверенности
     * Поле UF_CRM_21_1750336434 из смарт-процесса entityId = 1112
     */
    private function getPowerOfAttorneyFormsList()
    {
        $this->getListFieldValues('UF_CRM_21_1750336434');
    }

    /**
     * Обработка запросов к субъектам правоотношений
     */
    private function handleSubjects($action = '')
    {
        switch ($this->method) {
            case 'GET':
                if ($action === 'list' || $action === '') {
                    $this->getLegalSubjectsList();
                }
            default:
                $this->sendError('Метод не поддерживается', 405);
        }
    }

    /**
     * Получение списка субъектов правоотношений
     * Поле UF_CRM_21_1750334868 из смарт-процесса entityId = 1112
     */
    private function getLegalSubjectsList()
    {
        $this->getListFieldValues('UF_CRM_21_1750334868');
    }

    /**
     * Обработка запросов к субъектам правоотношений
     */
    private function handleStatuses($action = '')
    {
        switch ($this->method) {
            case 'GET':
                if ($action === 'list' || $action === '') {
                    $this->getStatusesList();
                }
            default:
                $this->sendError('Метод не поддерживается', 405);
        }
    }

    /**
     * Получение списка статусов доверенности
     * Поле UF_CRM_21_1749623950 из смарт-процесса entityId = 1112
     */
    private function getStatusesList()
    {
        $this->getListFieldValues('UF_CRM_21_1749623950');
    }

    /**
     * Универсальный метод для получения значений полей типа "Список"
     *
     * @param  string  $fieldCode  Код пользовательского поля
     * @return void
     */
    private function getListFieldValues($fieldCode)
    {
        try {
            if (! Loader::includeModule('main')) {
                $this->sendError('Main модуль не установлен');
            }

            $field = UserFieldTable::getList([
                'select' => [
                    'ID',
                    'USER_TYPE_ID',
                ],
                'filter' => [
                    'FIELD_NAME' => $fieldCode,
                ],
            ])->fetch();

            if (! $field) {
                $this->sendError('Поле '.$fieldCode.' не найдено', 404);
            }

            // Проверяем, что это поле типа "Список"
            if ($field['USER_TYPE_ID'] !== 'enumeration') {
                $this->sendError('Поле '.$fieldCode.' не является полем типа "Список"', 400);
            }

            // Получаем элементы списка
            $items = [];

            $enum = new CUserFieldEnum;
            $userFieldManager = $enum->GetList(
                [], ['USER_FIELD_ID' => $field['ID']],
            );

            while ($item = $userFieldManager->fetch()) {
                $items[] = [
                    'value' => (string) $item['VALUE'],
                    'id' => (string) $item['ID'],
                ];
            }

            $this->sendSuccess($items);

        } catch (Exception $e) {
            $this->sendError('Ошибка при получении списка поля '.$fieldCode.': '.$e->getMessage());
        }
    }

    /**
     * Обработка запросов к областям деятельности
     */
    private function handleActivityAreas($action = '')
    {
        switch ($this->method) {
            case 'GET':
                if ($action === 'list' || $action === '') {
                    $this->getActivityAreasList();
                }
            default:
                $this->sendError('Метод не поддерживается', 405);
        }
    }

    /**
     * Получение списка областей деятельности представителя по доверенности
     * Элементы смарт-процесса с entityId = 1134
     * Возвращает [{"value": TITLE, "id": ID}]
     */
    private function getActivityAreasList()
    {
        try {
            $entityId = 1134;

            // Получаем фабрику для работы со смарт-процессами
            $factory = Container::getInstance()->getFactory($entityId);

            if (! $factory) {
                $this->sendError('Смарт-процесс с ID '.$entityId.' не найден', 404);
            }

            // Создаем объект для выборки данных
            $dataClass = $factory->getDataClass();

            $result = $dataClass::getList([
                'select' => [
                    'ID',
                    'TITLE',
                ],
                'filter' => [],
                'order' => ['TITLE' => 'ASC'],
            ]);

            $activityAreas = [];

            while ($item = $result->fetch()) {
                $activityAreas[] = [
                    'value' => (string) ($item['TITLE'] ?? ''),
                    'id' => (string) $item['ID'],
                ];
            }

            $this->sendSuccess($activityAreas);

        } catch (Exception $e) {
            $this->sendError('Ошибка при получении списка областей деятельности: '.$e->getMessage());
        }
    }

    /**
     * Обработка запросов к директору компании
     */
    private function handleDirector($action = '')
    {
        switch ($this->method) {
            case 'GET':
                if ($action === '') {
                    $this->getDirector();
                }
            default:
                $this->sendError('Метод не поддерживается', 405);
        }
    }

    /**
     * Получение директора текущей компании
     */
    private function getDirector()
    {
        try {

            Loader::includeModule('intranet');
            $headId = CIntranetUtils::GetDepartmentManagerID($this::HEAD_DEPARTMENT_ID, true);
            $res = CUser::GetByID($headId);
            $director = $res->Fetch();
            $this->sendSuccess($this->formatUser($director));

        } catch (Exception $e) {
            $this->sendError('Ошибка при получении списка областей деятельности: '.$e->getMessage());
        }
    }

    /**
     * Обработка запросов к полномочиям
     */
    private function handleFile($action = '')
    {
        switch ($this->method) {
            case 'GET':
                if ($action === 'info') {
                    $this->getFileInfo();
                }
            case 'POST':
                if ($action === 'upload') {
                    $this->uploadFile();
                }
            case 'PATCH':
            case 'PUT':
            case 'DELETE':
            default:
                $this->sendError('Метод не поддерживается', 405);
        }
    }

    // Загрузка файла
    private function uploadFile()
    {
        $file = $_FILES['file'];

        try {
            $res = \CFile::SaveFile($file, Application::getDocumentRoot().'/upload/documents/'.$file['name']);
            $this->sendSuccess($res);
        } catch (Exception $err) {
            $this->sendError($err->getMessage());
        }
    }

    // Получение параметров файла
    private function getFileInfo()
    {
        try {
            $fileId = (int) $this->request->getQuery('id');
            if (! $fileId) {
                $this->sendError('Не удалось получить ID файла');
            }

            $file = CFile::GetById($fileId)->fetch();
            if ($file) {
                $this->sendSuccess([
                    'name' => $file['FILE_NAME'],
                    'url' => $file['SRC'],
                ]);
            }
        } catch (Exception $err) {
            $this->sendError($err->getMessage());
        }
    }

    /**
     * Обработка запросов к полномочиям
     */
    private function handleProject(string $action = '')
    {
        switch ($this->method) {
            case 'GET':
                if ($action === '' || $action === 'info') {
                    $this->getProject();
                }
            case 'POST':
                if ($action === 'add') {
                    $this->addProject();
                }
            case 'PATCH':
                if ($action === 'update') {
                    $this->updateProject();
                }
            default:
                $this->sendError('Метод не поддерживается', 405);
        }
    }

    // Получение элемента смарт-процесса ПРОЕКТ ДОВЕРЕННОСТИ
    private function getProject()
    {
        try {
            $itemId = (int) $this->request->getQuery('id');
            if (! $itemId) {
                $this->sendError('Указан неправильный ID проекта');
            }

            $entityTypeId = 1112;

            $factory = Container::getInstance()->getFactory($entityTypeId);
            if (! $factory) {
                $this->sendError('Смарт-процесс с ID '.$entityTypeId.' не найден', 404);
            }

            $item = $factory->getItem($itemId);
            if ($item) {
                $fields = $item->getData();

                foreach ($fields['UF_CRM_21_1750672577'] as $reprId) {
                    $fields['privateRepresentatives'][] = $this->getRepresentativeById((int) $reprId);
                }

                $fields['company'] = $this->formatCompany($this->getCompanyById((int) $fields['UF_CRM_21_1750400866']));
                $fields['legalRepresentative'] = $this->formatContactToRepresentative($this->getContactById((int) $fields['UF_CRM_21_1750669595']));

                $fields['executiveAuthority'] = $this->formatUser($this->getUserById((int) $fields['UF_CRM_21_1750401106']));

                $fields['initiator'] = $this->formatUser($this->getUserById((int) $fields['UF_CRM_21_1749039569']));

                $this->sendSuccess($this->formatProject($fields));
            } else {
                $this->sendError("Не удалось найти элемент из смарт-процесса с ID $entityTypeId с $itemId");
            }
        } catch (Exception $err) {
            $this->sendError($err->getMessage());
        }
    }

    // Добавление элемента смарт-процесса ПРОЕКТ ДОВЕРЕННОСТИ
    private function addProject()
    {
        try {
        } catch (Exception $err) {
            $this->sendError($err->getMessage());
        }
    }

    // Обновление элемента смарт-процесса ПРОЕКТ ДОВЕРЕННОСТИ
    private function updateProject()
    {
        try {
        } catch (Exception $err) {
            $this->sendError($err->getMessage());
        }
    }

    private function formatProject($project)
    {
        return [
            'id' => (string) $project['ID'],
            'status' => (string) $project['UF_CRM_21_1749623950'], // Статус проекта
            'subjectId' => (string) $project['UF_CRM_21_1750334868'], // Субъект правоотношения
            'isCancelled' => (bool) $project['UF_CRM_21_1752844977'], // Флаг: Проект отменён
            'isReadonly' => (bool) $project['UF_CRM_21_1756734803'], // Флаг: Только просмотр
            'attorneyData' => [
                'initiator' => $project['initiator'], // Инициатор
                'file' => (string) $project['UF_CRM_21_1749041076'], // Файл
                'internalNumber' => (string) $project['UF_CRM_21_1750312821'], // Внутренний номер
                'externalNumber' => (string) $project['UF_CRM_21_1750312953'], // Внешний номер
                'issueDate' => $project['UF_CRM_21_1750336284']->format('d.m.Y'), // Дата выпуска
                'startDate' => $project['UF_CRM_21_1750336322']->format('d.m.Y'), // Дата начала
                'endDate' => $project['UF_CRM_21_1750336382']->format('d.m.Y'), // Дата окончания
                'issueForm' => (string) $project['UF_CRM_21_1750336434'], // Форма выпуска
                'template' => (string) $project['UF_CRM_21_1750336735'], // Шаблон
                'system' => (string) $project['UF_CRM_21_1750336927'], // Система
                'status' => (string) $project['UF_CRM_21_1749623950'], // Статус проекта
                'copiesCount' => (int) $project['UF_CRM_21_1750337170'], // Кол-во экзепляров
                'justification' => $project['UF_CRM_21_1750337249'], // Обоснование необходимости
                'additionalInfo' => $project['UF_CRM_21_1750415775'], // Иные данные
                'activityArea' => $project['UF_CRM_21_1750748727'], // Область деятельности
                'isNotarialPowerOfAttorney' => $project['UF_CRM_21_1750738042'], // Нотариальная доверенность?
            ],
            'authorities' => [
                'values' => $project['UF_CRM_21_1749040674'], // Справочник полномочий
                'freeForm' => implode('', $project['UF_CRM_21_1750676047']), // Текст полномочий в свободной форме
                'isAuthoritiesInFreeForm' => $project['UF_CRM_21_1750675956'], // Полномочия в свободной форме?
            ],
            'legalRepresentative' => [
                'basePowerDocument' => $project['UF_CRM_21_1750335940'],
                'company' => $project['company'],
                'representative' => $project['legalRepresentative'],
            ],
            'privateRepresentatives' => $project['privateRepresentatives'],
        ];
    }

    /**
     * Получение данных из тела запроса
     */
    private function getRequestData()
    {
        $input = file_get_contents('php://input');

        if (empty($input)) {
            return [];
        }

        try {
            return Json::decode($input);
        } catch (Exception $e) {
            $this->sendError('Некорректный JSON в теле запроса');
        }
    }

    /**
     * Отправка успешного ответа
     */
    private function sendSuccess($data, $httpCode = 200)
    {
        http_response_code($httpCode);
        echo Json::encode(['data' => $data]);
        exit;
    }

    /**
     * Отправка ответа с ошибкой
     */
    private function sendError($message, $httpCode = 400)
    {
        http_response_code($httpCode);
        echo Json::encode(['error' => $message]);
        exit;
    }

    /**
     * Валидация обязательных полей
     */
    private function validateRequired($data, $requiredFields)
    {
        foreach ($requiredFields as $field) {
            if (! isset($data[$field]) || $data[$field] === '') {
                $this->sendError("Поле '{$field}' обязательно для заполнения");
            }
        }
    }

    /**
     * Логирование ошибок (можно расширить)
     */
    private function logError($message, $context = [])
    {
        // Здесь можно добавить логирование в файл или базу данных
        error_log('API Error: '.$message.' Context: '.Json::encode($context));
    }

    /**
     * Генерация случайно строки для уникальности ресурса
     */
    private function generateRandomString($length = 16)
    {
        return bin2hex(random_bytes($length / 2));
    }
}

// Инициализация и обработка запроса
$api = new API;
$api->handleRequest();
