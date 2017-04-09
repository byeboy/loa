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
        $files = File::all();
        return response()->json([
            'success' => true,
            'post' => [
                'files' => $files,
            ],
            'message' => '已获取所有文件信息',
        ]);
    }

    public function relation($type, $id = null) {
        $relations = [];
        foreach (File::whereHas($type, function ($file) use($id) {
            $file->where('filegable_id', $id);
        })->cursor() as $relation) {
            array_push($relations, $relation->id);
        }
        $files = File::whereNotIn('id', $relations)->get();
        return response()->json([
            'success' => true,
            'post' => [
                'relations' => $relations,
                'files' => $files,
            ],
            'message' => '已获取所有可关联的文件信息',
        ]);
    }

    public function create(Request $request) {
        if($request->hasFile('file')){
            $file = $request->file('file');
            if($file->isValid()){
                $type = $request->header('type');
                if(!$type) {
                    return response()->json([
                        'success' => false,
                        'post' => null,
                        'message' => '上传操作非法，请联系管理员',
                    ]);
                }
                $destinationPath = 'storage/uploads/files/'.$type;
                if(!file_exists($destinationPath)){
                    if(!mkdir($destinationPath)){
                        return response()->json([
                            'success' => false,
                            'post' => null,
                            'message' => '新建路径失败，请联系管理员',
                        ]);
                    }
                }
                $clientName = $file -> getClientOriginalName();     /*上传文件的名称*/
                $extension = $file -> getClientOriginalExtension(); /*上传文件的后缀*/
                $newName = md5(date('ymdhis').$clientName).".".$extension; /*设置新文件名*/
                if($this->check($clientName)) {
                    return response()->json([
                        'success' => false,
                        'post' => null,
                        'message' => '文件('.$clientName.')已存在',
                    ]);
                }
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
                            'message' => '文件('.$clientName.')上传成功',
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'post' => null,
                            'message' => '文件('.$clientName.')上传失败',
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'post' => null,
                        'message' => '文件('.$clientName.')上传失败',
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

    public static function down($id) {
        $file = File::find($id);
        return response()->download($file->url, $file->name);
    }

    public static function getZip($files, $zipName) {
//        return $files;
        //接收需下载的所有文件的路径
        //实例化zipArchive类
        $zip = new \ZipArchive();
        //创建空的压缩包
        $zipName = $zipName.'.zip';
        if($zip->open($zipName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) && count($files) !== 0){ //打开的方式来进行创建 若有则打开 若没有则进行创建
            //循环将要下载的文件路径添加到压缩包
            foreach ($files as $f) {
                $zip->addFile($f[0], basename($f[1]));
            }
            //关闭压缩包
            $zip->close();
            $headers = [
                'Content-Type' => 'Application/zip',
                'Content-Length' => filesize($zipName),
            ];
            /*return response()->json([
                'zip' => $zip,
                'name' => $zipName,
            ]);*/
            return response()->download($zipName, $zipName, $headers);
        }
        //实现文件的下载
        /*header('Content-Type:Application/zip');
        header('Content-Disposition:attachment; filename=' . $zipName);
        header('Content-Length:' . filesize($zipName));
        readfile($zipName);*/
        //删除生成的压缩文件
//        unlink($zipName);
    }

    public function check($name) {
        return File::where('name', $name)->exists();
    }
}