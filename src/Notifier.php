<?php
declare(strict_types=1);

namespace TwitchWatcher;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use TwitchWatcher\Collections\NotificationsCollection;
use TwitchWatcher\Collections\PersistableCollection;
use TwitchWatcher\Data\Condition;
use TwitchWatcher\Data\DataMapper;
use TwitchWatcher\Models\Notification;
use TwitchWatcher\Models\Vod;

class Notifier
{
    private DataMapper $dm;

    private PHPMailer $mail;

    private string $email = 'lwshpak@gmail.com';
    public function __construct(DataMapper $dm)
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

    public function getNotNotified() : PersistableCollection
    {
        $notifications = $this->dm->find(new NotificationsCollection())
                        ->where(new Condition(['is_notified', 'false', '=']))
                        ->do();
        return $notifications;
    }

    public function notify(Notification $notification): bool
    {
        try {
            $vod = $this->dm->find(new Vod())->byId($notification->vod_id)->one();
            $this->mail->setFrom('lwshpak@gmail.com', 'App');
            $this->mail->addAddress($this->email, 'Me');
            $this->mail->Subject = 'Twitch VOD Notification';
            $body = "Новый повтор {$vod->name} {$vod->description} от {$vod->uploadDate}. Ссылка {$vod->url}";
            $this->mail->Body = $body;

            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.gmail.com';
            $this->mail->SMTPAuth = true;
            $this->mail->SMTPSecure = 'tls';
            $this->mail->Username = 'lwshpak@gmail.com';
            $this->mail->Password = 'emkt riuq gnck zofe ';
            $this->mail->Port = 587;
            $this->mail->SMTPDebug = 4;

            $notification->is_notified = true;
            //TODO фикс таймзону
            $notification->notification_timestamp = (new \DateTime('now'))->format('Y-m-d h:i:s');
            $this->dm->insert($notification);

            $this->mail->send();

        } catch(Exception $e) {
            echo $e->errorMessage();
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
        return true;
    }
    
}