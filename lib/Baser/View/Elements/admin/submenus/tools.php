<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] ユーティリティメニュー
 */
?>


<tr>
	<th>ユーティリティメニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link(__d('baser', 'ユーティリティトップ'), ['controller' => 'tools', 'action' => 'index', 'plugin' => null]) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', '環境情報'), ['controller' => 'site_configs', 'action' => 'info', 'plugin' => null]) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'データメンテナンス'), ['controller' => 'tools', 'action' => 'maintenance', 'plugin' => null]) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'ログメンテナンス'), ['controller' => 'tools', 'action' => 'log', 'plugin' => null]) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'スキーマファイル生成'), ['controller' => 'tools', 'action' => 'write_schema']) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'スキーマファイル読込'), ['controller' => 'tools', 'action' => 'load_schema']) ?></li>
		</ul>
	</td>
</tr>
