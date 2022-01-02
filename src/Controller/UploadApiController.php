<?php


namespace App\Controller;


use App\Consts\Role;
use App\Util\HttpUtils;
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
    #[IsGranted(Role::USER)]
    #[Route(path: "/api/upload", name: "api_upload", methods: ['POST'])]
    public function upload(Request $request, string $projectDir, string $imagePath): Response {
        /** @var UploadedFile $file */
        $file = $request->files->get("file");
        if (is_null($file)) {
            return HttpUtils::wrapperFail("上传失败");
        }
        // generate a random name for the file but keep the extension
        $filename = uniqid(prefix: 'Image_').".".$file->getClientOriginalExtension();
        $path = $projectDir.'/public'.$imagePath;
        $file->move($path, $filename); // move the file to a path
        return HttpUtils::wrapperSuccess([
            'url'=> $request->getSchemeAndHttpHost().$imagePath.$filename,
            'name'=> $filename
        ]);
    }
}