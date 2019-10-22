<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Trade;

class PayController extends Controller
{
	//必须在控制器头部初始化关闭csrf防护，不能在方法中$this->$enableCsrfValidation = false;
	public $enableCsrfValidation = false;
	
    /**
     * 支付宝同步通知 由于无法在公网服务器测试，因此先用get-同步通知来处理业务逻辑
     */
    public function actionAlipay_return()
    {
        $get = json_decode($_GET['i'] ,true);
        //josn 转码时 会丢失 '+' 数据被空格替代了
        $get['sign'] = str_replace(' ', '+', $get['sign']);
        require_once './../vendor/alipay/config.php';
        require_once './../vendor/alipay/pagepay/service/AlipayTradeService.php';
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($get);
        if ($result) {
            //--------------------------------------------------------------//
            //这里本应 验证：total_amount交易金额 auth_app_id/app_id应用ID seller_id商户ID 订单状态 是否与数据库匹配
            $trade = Trade::findOne(['out_trade_no'=>$get['out_trade_no']]);
            //这里本应 验证：total_amount交易金额 auth_app_id/app_id应用ID seller_id商户ID 是否与数据库匹配
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $trade->trade_no = $get['trade_no'];
                $trade->status = 1;//同步/异步通知成功验签
                $trade->flows->status = 0;//流水执行周期已完成
                $trade->flows->endtime = time();//流水执行周期已完成
                $trade->flows->in->balance = $trade->flows->in->balance + intval($trade->money);//用户对应的余额修改
                if (!$trade->save()) {
                    throw new \Exception();
                }
                if (!$trade->flows->save()) {
                    throw new \Exception();
                }
                if (!$trade->flows->in->save()) {
                    throw new \Exception();
                }
                $transaction->commit();
                Yii::$app->session->setFlash('paySuccess');
            } catch (\Exception $e) {
                file_put_contents('./../log/alipaylog.txt','|---'.date('YmdHis').'*'.json_encode($e).'*'.$trade->id.'---|',FILE_APPEND);
                $transaction->rollback();
                Yii::$app->session->setFlash('payFail');
            }
            //-------------------------------------------------------------//
        } else {
            Yii::$app->session->setFlash('payFail'); 
        }
        return $this->redirect(['vip/finance','option'=>'recharge']);
    }

    /**
     * 支付宝异步通知
     */
    public function actionAlipay_notify()
    {
        $post = $_POST;
        if (!$post) {
            return $this->goHome();
        }
        require_once './../vendor/alipay/config.php';
        require_once './../vendor/alipay/pagepay/service/AlipayTradeService.php';
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($post);
        if ($result) {
            //--------------------------------------------------------------//
            if($post['trade_status'] == 'TRADE_FINISHED') {
            //判断该笔订单是否在商户网站中已经做过处理
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
            //如果有做过处理，不执行商户的业务程序
            //注意：
            //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            } elseif ($post['trade_status'] == 'TRADE_SUCCESS') {
            //判断该笔订单是否在商户网站中已经做过处理
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
            //如果有做过处理，不执行商户的业务程序            
            //注意：
            //付款完成后，支付宝系统发送该交易状态通知
            }
            //这里本应 验证：订单状态 total_amount交易金额 auth_app_id/app_id应用ID seller_id商户ID 是否与数据库匹配
            $trade = Trade::findOne(['out_trade_no'=>$post['out_trade_no']]);
            //这里本应 验证：total_amount交易金额 auth_app_id/app_id应用ID seller_id商户ID 是否与数据库匹配
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $trade->trade_no = $post['trade_no'];
                $trade->status = 1;//同步/异步通知成功验签
                $trade->flows->status = 0;//流水执行周期已完成
                $trade->flows->endtime = time();//流水执行周期已完成
                $trade->flows->in->balance = $trade->flows->in->balance + intval($trade->money);//用户对应的余额修改
                if (!$trade->save()) {
                    throw new \Exception();
                }
                if (!$trade->flows->save()) {
                    throw new \Exception();
                }
                if (!$trade->flows->in->save()) {
                    throw new \Exception();
                }
                $transaction->commit();
                //echo "success";                
            } catch (\Exception $e) {
                file_put_contents('./../log/alipaylog.txt','|---'.date('YmdHis').'*'.json_encode($e).'*'.$trade->id.'---|',FILE_APPEND);
                $transaction->rollback();
                //echo "fail";
            }
            //-------------------------------------------------------------//
        } else {
            file_put_contents('./../log/alipaylog.txt', '<---'.date('YmdHis').'*'.json_encode($post).'--->',FILE_APPEND);
            //echo "fail";
        }
        exit();
        //最后必须echo success 或者 echo fail 再接exit;
    }

}
