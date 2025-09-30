<?php
require_once __DIR__.'/crest.php';

$install_result = CRest::installApp();

$handlerBackUrl = ($_SERVER['HTTPS'] === 'on' || $_SERVER['SERVER_PORT'] === '443' ? 'https' : 'http').'://'
    .$_SERVER['SERVER_NAME']
    .(in_array($_SERVER['SERVER_PORT'], ['80', '443'], true) ? '' : ':'.$_SERVER['SERVER_PORT'])
    .'/ac/index.php';

$result = CRest::call(
    'placement.bind',
    [
        'PLACEMENT' => 'CRM_DYNAMIC_1112_DETAIL_TAB',
        'HANDLER' => $handlerBackUrl,
        'TITLE' => 'Доверенность',
        'OPTIONS' => [
            'SORT' => 1,
        ],
    ]
);

file_put_contents(__DIR__.'/logs.txt', var_export($result, 1));

if ($install_result['rest_only'] === false) { ?>
<head>
	<script src="//api.bitrix24.com/api/v1/"></script>
	<?if ($install_result['install'] == true) { ?>
	<script>
		BX24.init(function(){
			BX24.installFinish();
		});
	</script>
	<?}?>
</head>
<body>
	<?if ($install_result['install'] == true) { ?>
        Установка прошла успешно
	<?} else { ?>
        Произошла ошибка при установке приложения
	<?}?>
</body>
<?}
