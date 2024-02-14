<?php
declare(strict_types=1);

namespace TwitchWatcher;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Notifier
{
    private DataManager $dm;

    private PHPMailer $mail;

    private string $email = 'lwshpak@gmail.com';
    public function __construct(DataManager $dm)
    {
        $this->dm = $dm;
        $this->mail = new PHPMailer(true);
    }
    public function notifyAll() : bool
    {
        $notifications = $this->getNotNotified();
        foreach($notifications as $notification) {
            $this->notify($notification);
        }
        return true;
    }

    public function getNotNotified() : array|false
    {
        $notifications = $this->dm->select('notifications', '*', 'WHERE is_notified IS FALSE');
        if (empty($notifications)) {
            return false;
        } else {
            return $notifications;
        }
    }

    public function notify(array $notification): bool
    {
        if (empty($notification)) {
            return false;
        }
        try {
            $vod = $this->dm->select('vods', '*', "id = {$notification['vod_id']}");
            
            if (empty($vod)) {
                throw new \LogicException("Не найдено повтора, связанного с оповещением");
            }
            $vod = $vod[0];
            $this->mail->setFrom('lwshpak@gmail.com', 'App');
            $this->mail->addAddress($this->email, 'Me');
            $this->mail->Subject = 'Twitch VOD Notification';
            $body = "Новый повтор {$vod['name']} {$vod['description']} от {$vod['uploadDate']}. Ссылка {$vod['url']}";
            $this->mail->Body = $body;

            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.gmail.com';
            $this->mail->SMTPAuth = true;
            $this->mail->SMTPSecure = 'tls';
            $this->mail->Username = 'lwshpak@gmail.com';
            $this->mail->Password = 'emkt riuq gnck zofe ';
            $this->mail->Port = 587;
            $this->mail->SMTPDebug = 4;

            $this->dm->update('notifications', ["is_notified" => true], "id = '{$notification['id']}'");

            $this->mail->send();

        } catch(Exception $e) {
            echo $e->errorMessage();
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
        return true;
    }
    
}