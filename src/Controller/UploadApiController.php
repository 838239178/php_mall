<?php


namespace App\Controller;


use App\Consts\Role;
use App\Util\HttpUtils;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UploadApiController extends AbstractController
{
    private HttpUtils $httpUtils;
    private LoggerInterface $logger;

    public function __construct(HttpUtils $httpUtils, LoggerInterface $logger)
    {
        $this->httpUtils = $httpUtils;
        $this->logger = $logger;
    }

    #[IsGranted(Role::USER)]
    #[Route(path: "/api/uploads/image", name: "api_upload", methods: ['POST'])]
    public function upload(Request $request, string $projectDir, string $imagePath): Response {
        /** @var UploadedFile $file */
        $file = $request->files->get("file");
        if (is_null($file)) {
            return $this->httpUtils->wrapperFail("上传失败 文件为空");
        }
        // generate a random name for the file but keep the extension
        $filename = uniqid(prefix: 'Image_').".".$file->getClientOriginalExtension();
        $path = $projectDir.'/public'.$imagePath;
        $this->logger->warning("uploading path: ".$path);
        try {
            $file->move($path, $filename); // move the file to a path
        } catch (\Exception $e) {
            return $this->httpUtils->wrapperFail("文件路径$path,异常$e");
        }
        return $this->httpUtils->wrapperSuccess([
            'url'=> $request->getSchemeAndHttpHost().$imagePath.$filename,
            'name'=> $filename
        ]);
    }

    #[Route("/api/upload/html", name: "api_upload_html", methods: ["POST"])]
    public function uploadHtml(Request $request): Response {
        return $this->httpUtils->wrapperSuccess();
    }
}