<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Database\Migration;

use BaserCore\Utility\BcUtil;
use Migrations\AbstractMigration;
use Migrations\Table;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * BcMigration
 */
class BcMigration extends AbstractMigration
{

    /**
     * テーブルを取得する
     *
     * プレフィックスをテーブル名に反映する
     *
     * @param string $tableName Table Name
     * @param array $options Options
     * @return \Migrations\Table
     * @checked
     * @noTodo
     */
    public function table(string $tableName, array $options = []): Table
    {
        $prefix = BcUtil::getCurrentDbConfig()['prefix'];
        return parent::table($prefix . $tableName);
    }

}
