<?php


namespace App\Controller;


namespace App\Controller;

use App\DTO\RegisterDTO;
use App\Entity\UserInfo;
use App\Util\HttpUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    private EntityManagerInterface $em;

    /**
     * SecurityController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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

    public function loginApi(#[CurrentUser] UserInfo $userInfo): Response
    {
        if ($userInfo == null) {
            return $this->json("未检测到登录信息", Response::HTTP_UNAUTHORIZED);
        }
        return $this->json([
            'data' => [
                'userId' => $userInfo->getUserId(),
                'username' => $userInfo->getUsername(),
                'nickName' => $userInfo->getNickName(),
                'avatar' => $userInfo->getAvatar()
            ],
            'token' => 123123,
        ]);
    }

    #[Route(path: "/api/register", name: "api_register", methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): Response
    {
        $registerInfo = HttpUtils::wrapperRequest($request, RegisterDTO::class);
        if ($registerInfo instanceof RegisterDTO) {
            //validation
            $errors = $validator->validate($registerInfo);
            if (count($errors) > 0) {
                return HttpUtils::wrapperErrors($errors);
            }

            $userInfo = new UserInfo();
            $userInfo->setSalt(uuid_create());
            $res = $passwordHasher->hashPassword($userInfo, $registerInfo->getPassword());
            $userInfo->setPassword($res);
            $userInfo->setUsername($registerInfo->getUsername());
            $userInfo->setEmail($registerInfo->getEmail());
            //persist
            $this->em->persist($userInfo);
            $this->em->flush();
            return $this->json([
                'success'=>true,
                'message'=>'注册成功'
            ]);
        }
        return $this->json([
            'success'=>false,
            'message'=>'请求异常'
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
    }
}