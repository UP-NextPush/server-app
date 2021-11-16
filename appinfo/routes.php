<?php

return [
	'routes' => [
		[
			'name' => 'UnifiedPushProvider#setKeepalive',
			'url' => '/setkeepalive/',
			'verb' => 'PUT',
		],
		[
			'name' => 'UnifiedPushProvider#createDevice',
			'url' => '/device/',
			'verb' => 'PUT',
		],
		[
			'name' => 'UnifiedPushProvider#syncDevice',
			'url' => '/device/{deviceId}',
			'verb' => 'GET',
		],
		[
			'name' => 'UnifiedPushProvider#deleteDevice',
			'url' => '/device/{deviceId}',
			'verb' => 'DELETE',
		],
		[
			'name' => 'UnifiedPushProvider#createApp',
			'url' => '/app/',
			'verb' => 'PUT',
		],
		[
			'name' => 'UnifiedPushProvider#deleteApp',
			'url' => '/app/{token}',
			'verb' => 'DELETE',
		],
		[
			'name' => 'UnifiedPushProvider#push',
			'url' => '/push/{token}',
			'verb' => 'POST',
		],
		[
			'name' => 'UnifiedPushProvider#unifiedpushDiscovery',
			'url' => '/push/{token}',
			'verb' => 'GET',
		],
	]
];
