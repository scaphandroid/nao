<?php

namespace NAO\PlatformBundle\Services\SendMail;

use NAO\PlatformBundle\Entity\User;
use Symfony\Component\Templating\EngineInterface;
use \Symfony\Bundle\SwiftmailerBundle;

class NAOSendMail
{
    private $mailer;
    private $message;
    private $user;
    private $validation;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMail(User $user, $validation) {
        $this->validation = $validation;
        $this->user = $user;
        $this->message = \Swift_Message::newInstance();
        $this->message
            ->setSubject('Votre demande de compte naturaliste')
            ->setFrom(array('nao@nao.com' => 'Nos Amis les Oiseaux'))
            ->setTo($this->user->getEmail());
        if ($validation) {
            $this->message->setBody('<p>Bonjour,</p><p>Votre compte naturaliste est validé. Vous pouvez vous <a href="http://raphaeloff.net/nao/web/connexion">connecter</a> dès maintenant et valider les observations des particuliers.</p><p>A bientôt.</p><p>L\'équipe</p>', 'text/html');
        }
        else {
            $this->message->setBody('<p>Bonjour,</p><p>Votre compte naturaliste n\'a pas été validé par nos équipes.</p><p>Si vous aviez déjà un compte particulier, vous pouvez continuer à saisir vos observations. Si vous n\'en aviez pas, nous vous en avons créer un avec le nom d\'utilisateur et le mot de passe que vous aviez saisi lors de votre demande.</p><p> <a href="http://raphaeloff.net/nao/web/connexion">Connectez-vous</a> dès maintenant et saisissez des observations.</p><p>A bientôt.</p><p>L\'équipe</p>', 'text/html');
        }
        $this->mailer->send($this->message);
    }

}