<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Wish;

/**
 * This command echoes the first argument that you have entered.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CronController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'hello world')
    {
        $this->actionWish();
    }

    /**
     * 计划每天运行一次的定时任务
     * 心愿转账和邮件提醒
     */
    public function actionWish()
    {
        echo "-------------------------------------------- \n";
        echo date("y-m-d H:i:s").", Start Wish/Crontab:\n";
        echo "-------------------------------------------- \n";

        $result = Wish::Crontab();

        foreach ($result as $key => $value) {
            echo "-------------------------------------------- \n";
            echo 'wish_id:'.$key."\n";
            echo 'success:'.$value['success']."\n";
            echo 'insufficient:'.$value['insufficient']."\n";
            echo 'error:'.$value['error']."\n";
            echo "-------------------------------------------- \n";
        }

        return ExitCode::OK;
    }
}
