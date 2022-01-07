<?php


namespace App\Controller;


namespace App\Controller;

use App\Consts\Role;
use App\DTO\RegisterDTO;
use App\Entity\UserInfo;
use App\Util\HttpUtils;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    private EntityManagerInterface $em;
    private MailerInterface $mailer;
    private UserPasswordHasherInterface $passwordHasher;
    private HttpUtils $httpUtils;

    /**
     * SecurityController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, MailerInterface $mailer, UserPasswordHasherInterface $passwordHasher, HttpUtils $httpUtils)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->passwordHasher = $passwordHasher;
        $this->httpUtils = $httpUtils;
    }


    #[Route(path: "/login", name: "login")]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@EasyAdmin/page/login.html.twig', [
            // parameters usually defined in Symfony login forms
            'error' => $error,
            'last_username' => $lastUsername,

            'page_title' => '管理员登录',

            // the string used to generate the CSRF token. If you don't define
            // this parameter, the login form won't include a CSRF token
            'csrf_token_intention' => 'authenticate',

            // the label displayed for the username form field (the |trans filter is applied to it)
            'username_label' => '用户名',

            'target_path' => $this->generateUrl('admin_dashboard'),

            // the label displayed for the password form field (the |trans filter is applied to it)
            'password_label' => '密码',

            // the label displayed for the Sign In form button (the |trans filter is applied to it)
            'sign_in_label' => '登录',

            // whether to enable or not the "forgot password?" link (default: false)
            'forgot_password_enabled' => true,

            // the label displayed for the "forgot password?" link (the |trans filter is applied to it)
            'forgot_password_label' => '忘记密码',

            // whether to enable or not the "remember me" checkbox (default: false)
            'remember_me_enabled' => true,

            // whether to check by default the "remember me" checkbox (default: false)
            'remember_me_checked' => true,

            // the label displayed for the remember me checkbox (the |trans filter is applied to it)
            'remember_me_label' => '记住我',
        ]);
    }

    function getRandStr(int $length): string
    {
        //字符组合
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $len = strlen($str) - 1;
        $randstr = '';
        for ($i = 0; $i < $length; $i++) {
            $num = mt_rand(0, $len);
            $randstr .= $str[$num];
        }
        return $randstr;
    }

    #[Route(path: "/api/password", methods: ['PATCH'])]
    public function changePwd(Request $request): Response
    {
        $oldPwd = $request->get("oldPwd");
        $newPwd = $request->get("newPwd");
        /** @var UserInfo $user */
        $user = $this->getUser();
        if (isset($oldPwd) and isset($newPwd)) {
            if ($this->passwordHasher->isPasswordValid($user, $oldPwd)) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $newPwd));
                $this->em->flush();
                return $this->httpUtils->wrapperSuccess();
            }
            return $this->httpUtils->wrapperFail("密码错误");
        }
        return $this->httpUtils->wrapperFail("参数不能为空");
    }

    #[Route(path: "/api/emailCode/{email}", name: "api_sendEmailCode", methods: ['GET'])]
    public function getEmailCode(string $email, LoggerInterface $logger, Session $session): Response
    {
        $code = $this->getRandStr(5);
        //save code
        $emailSubject = (new TemplatedEmail())
            ->to($email)
            ->from(new Address('838239178@qq.com', 'PHP MALL技术团队'))
            ->subject("[PHP MALL] 您正在进行注册活动，请验证你的邮箱")
            // path of the Twig template to render
            ->htmlTemplate('emails/register.html.twig')
            // pass variables (name => value) to the template
            ->context([
                'code' => $code,
            ]);
        try {
            $this->mailer->send($emailSubject);
            $session->set($email, $code);
            return $this->json([
                "message" => "发送成功"
            ]);
        } catch (TransportExceptionInterface $e) {
            $logger->error($e->getTraceAsString());
            return $this->json([
                "message" => "发送失败,稍后再试"
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    #[Route(path: "/api/register", name: "api_register", methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, Session $session): Response
    {
        $registerInfo = $this->httpUtils->wrapperRequest($request, RegisterDTO::class);
        if ($registerInfo instanceof RegisterDTO) {
            //validation
            $errors = $validator->validate($registerInfo);
            if (count($errors) > 0) {
                return $this->httpUtils->wrapperErrors($errors);
            }
            $code = $session->get($registerInfo->getEmail());
            if ($code != $registerInfo->getEmailCode()) {
                return $this->httpUtils->wrapperFail("验证码有误");
            }

            $userInfo = new UserInfo();
            $userInfo->setSalt(uuid_create());
            $res = $passwordHasher->hashPassword($userInfo, $registerInfo->getPassword());
            $userInfo->setPassword($res);
            $userInfo->setRoles([Role::USER]);
            $userInfo->setUsername($registerInfo->getUsername());
            $userInfo->setEmail($registerInfo->getEmail());
            $userInfo->setNickName(uniqid(prefix: "用户_"));
            //persist
            $this->em->persist($userInfo);
            $this->em->flush();
            //clear code
            $session->remove($registerInfo->getEmail());
            return $this->json([
                'success' => true,
                'message' => '注册成功'
            ]);
        }
        return $this->json([
            'success' => false,
            'message' => '请求异常'
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
    }
}