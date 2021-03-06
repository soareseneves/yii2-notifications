<?php

namespace soareseneves\notifications\channels;

use Yii;
use soareseneves\notifications\Channel;
use soareseneves\notifications\Notification;
use soareseneves\firebase\FirebaseNotifications;
use app\models\Token;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

class FirebaseChannel extends Channel
{

    private function logError($message){
        file_put_contents('/home/sigiweb/www/app/runtime/logs/log_notifications_'.date("j.n.Y").'.log', $message . PHP_EOL, FILE_APPEND);
    }

    public function send(Notification $notification)
    {
        $service = new FirebaseNotifications(['authKey' => 'AAAALCabrjs:APA91bF4HFKFkdFtWmaAw1E7XjDYdvIybpbxnySO-HulnO_M3h25YL-nhIBLjd3nDMNgEU8-PCCs72IgrBRwb7mHM_1IvRQUjtUGgIdg5Ec9uAW7sCVDE50hGPU8aALv-EfMldT-lbnr']);

        $user_ids = $this->recipients($notification);

        if (!is_null($user_ids)){
            $tokens = Token::find()->andFilterwhere([
                            'and',
                            ['in', 'user_id', $user_ids],
                            ['=', 'type', '4']
                        ])->asArray()->all();

            $tokens = ArrayHelper::getColumn($tokens, 'code');

            $notificationData = $notification->getData();

            $notificationData['notification_id'] = $notificationData['id'];
            unset($notificationData['id']);
            unset($notificationData['table_name']);
            unset($notificationData['table_id']);
            unset($notificationData['scheduled_date']);
            unset($notificationData['public_notification']);
            unset($notificationData['user_permission']);
            unset($notificationData['exclude_owner']);
            unset($notificationData['event_class']);

            $notificationData['url'] = \Yii::$app->urlManager->createAbsoluteUrl(json_decode($notificationData['click_action'], 'https'));

            $sendData = $service->sendNotification($tokens, ['notification' => $notificationData, 'data' => $notificationData]);

            $tokensToRemove = [];

            if (isset($sendData['results'])){
                foreach ($sendData['results'] as $tokenIndex => $result) {
                    if (isset($result['error'])){
                        $tokensToRemove[] = $tokens[$tokenIndex];
                    }
                }

                if (count($tokensToRemove) > 0){
                    Token::deleteAll(['code' => $tokensToRemove]);
                }
            }
        }

    }

}