<?php

use Bitrix\Main\Loader;
use Bitrix\Rest\PlacementTable;

class attorney_smarttab extends CModule
{
    public $MODULE_ID = 'attorney.smarttab';

    public $MODULE_VERSION;

    public $MODULE_VERSION_DATE;

    public $MODULE_NAME = 'Установщик вкладки Доверенности';

    public $MODULE_DESCRIPTION = 'Добавляет вкладку Доверенности в карточку смарт-процесса';

    const PLACEMENT = 'CRM_LEAD_DETAIL_TAB';

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__.'/version.php';
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    public function DoInstall()
    {
        global $APPLICATION;

        if (Loader::includeModule('rest')) {
            $handler = $this->getHandlerUrl();
            $existing = PlacementTable::getList([
                'filter' => [
                    '=PLACEMENT' => self::PLACEMENT,
                    '=PLACEMENT_HANDLER' => $handler,
                ],
            ])->fetch();

            if (! $existing) {
                PlacementTable::add([
                    'APP_ID' => 0,
                    'PLACEMENT' => self::PLACEMENT,
                    'TITLE' => 'Карточка доверенности',
                    'COMMENT' => 'Вкладка: карточка доверенности',
                    'PLACEMENT_HANDLER' => $handler,
                    // 'OPTIONS' => json_encode(
                    //     [
                    //         // 'TAB_ID' => 'power_of_attorney', // уникальный ID вашей вкладки
                    //         // 'SORT' => 100,
                    //     ]
                    // ),
                ]);
            }
        }

        // регистрируем модуль в системе
        RegisterModule($this->MODULE_ID);
    }

    public function DoUninstall()
    {
        if (Loader::includeModule('rest')) {
            $handler = $this->getHandlerUrl();

            $res = PlacementTable::getList([
                'filter' => [
                    '=PLACEMENT' => self::PLACEMENT,
                    '=PLACEMENT_HANDLER' => $handler,
                ],
            ]);

            while ($row = $res->fetch()) {
                PlacementTable::delete($row['ID']);
            }
        }

        UnRegisterModule($this->MODULE_ID);
    }

    private function getHandlerUrl()
    {
        // Определяем протокол
        $proto = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        // Берём хост без порта
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        // Удаляем порт, если он указан (например, example.com:8080 → example.com)
        $host = preg_replace('/:\d+$/', '', $host);

        return $proto.'://'.$host.'/ac/index.php';
    }
}
