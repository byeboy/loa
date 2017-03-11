<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/3/7
 * Time: 下午3:54
 */

namespace App\Http\Controllers;


use App\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index() {
        $files = File::with('uploader')->with('task')->all();
        return response()->json([
            'success' => true,
            'post' => [
                'files' => $files,
            ],
            'message' => '已获取所有文件信息',
        ]);
    }

    public function create(Request $request) {
        if($request->hasFile('file')){
            $file = $request->file('file');
            if($file->isValid()){
                $destinationPath = 'storage/uploads/files';
                $clientName = $file -> getClientOriginalName();     /*上传文件的名称*/
                $extension = $file -> getClientOriginalExtension(); /*上传文件的后缀*/
                $newName = md5(date('ymdhis').$clientName).".".$extension; /*设置新文件名*/
//                $path = $file -> move(app_path().'/storage/uploads',$newName);

                if($file -> move($destinationPath, $newName)) {
                    $f = File::create([
                        'name' => $clientName,
                        'url' => $destinationPath.'/'.$newName,
                    ]);
                    if($f !== null){
                        return response()->json([
                            'success' => true,
                            'post' => [
                                'file' => $f,
                            ],
                            'message' => '文件上传成功',
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'post' => null,
                            'message' => '文件上传失败',
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'post' => null,
                        'message' => '文件上传失败',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'post' => null,
                    'message' => '文件校验失败，请检查文件',
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '没有需上传的文件，请检查请求信息',
            ]);
        }

    }
}