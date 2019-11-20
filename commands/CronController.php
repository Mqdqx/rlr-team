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
        echo date("y-m-d H:i:s").", Start Wish::Crontab()\n";
        
        $result = Wish::Crontab();

        foreach ($result as $key => $value) {
            echo "++++++++++++++++++++++++++++++++++++++++++++ \n";
            echo 'wish_id:'.$key."\n";
            echo 'success:'.$value['success']."\n";
            echo 'insufficient:'.$value['insufficient']."\n";
            echo 'error:'.$value['error']."\n";
            echo "++++++++++++++++++++++++++++++++++++++++++++ \n";
        }
        echo "-------------------------------------------- \n";
        file_put_contents(dirname(__DIR__).'/log/cronlog.txt','|___'.date('YmdHis').'*'.json_encode($result).'___|'.PHP_EOL,FILE_APPEND);//写入日志
        return ExitCode::OK;
    }

    /**
     * 计划每天执行一次
     * 投票任务的自动终止
     */
    public function actionVote()
    {
        //注意console的date()为本初子午线时区，更换为
        $dawnYest = 86400+strtotime(date("Y-m-d"),time());//明天0点0分1秒
        $models = Vote::findAll(['status'=>1]);
        $result = [];
        foreach ($models as $key => $vote) {
            Yii::$app->session->set('team',$vote->team);
            //如果此状态为进行中的投票活动自动结束时间在 明天0-0-0到24-59-59中
            if ($vote->endtime>$dawnYest && $vote->endtime<$dawnYest+86400) {
                $minBallot = $vote->findMinballot();
                $noVote = $vote->noComplete();
                if (count($vote->res)==1) {
                    //如果只有一个候选者心愿则满足统计结果
                    //统计结果 发送邮件
                    $vote->statistics();
                } elseif ((!$minBallot) && $noVote) {
                    //若存在并行最小 并且还有团体成员没有投票
                    //发送邮件提醒未投票成员参与
                    foreach ($noVote as $k => $user) {
                        $mailer = Yii::$app->mailer->compose('vote',['vote_id'=>$vote->vote_id,'team_name'=>Yii::$app->session['team']->name ,'email'=>$user['email']]);
                        $mailer->setFrom(Yii::$app->params['senderEmail']);
                        $mailer->setTo($user['email']);
                        $mailer->setSubject('人恋人平台-投票活动提醒');
                        $mailer->send();
                    }
                    Yii::$app->session->setFlash('noMinballot');
                } elseif ((!$minBallot) && (!$noVote)) {
                    //若存在并行最小 并且所有成员都参与了投票
                    //结束时间延长一天 且 清空当前投票结果
                    $vote->reset();
                } else {
                    //满足统计结果
                    //统计结果 发送邮件
                    $vote->statistics();
                }
                //$result[$vote->vote_id]缓存结果
                $flash = ['voteoneSuccess','voteoneFail','noMinballot','reset','statistics'];
                foreach ($flash as $key => $value) {
                    if (Yii::$app->session->hasFlash($value)) {$result[$vote->vote_id][] = Yii::$app->session->getFlash($value);}
                }
                file_put_contents(dirname(__DIR__).'/log/votelog.txt','|___'.date('YmdHis').'*'.json_encode($result).'___|'.PHP_EOL,FILE_APPEND);//写入日志                
            }
        }
        return ExitCode::OK;
    }
}
