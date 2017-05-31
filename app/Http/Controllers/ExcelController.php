<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/5/7
 * Time: 上午9:32
 */

namespace App\Http\Controllers;


use App\Cabinet;
use App\Fan;
use App\Model;
use App\Part;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    //Excel文件导出功能
    public function export($fileType = 'xls'){
        $modelsData = [];
        $models = Model::with('records.operator')->get();
        foreach ($models as $model) {
            $modelsData[] = ['名称', '库存数量', '最近更新'];
            $modelsData[] = [$model->name, $model->count, $model->updated_at];
            if(!$model->records->isEmpty()) {
                $modelsData[] = ['', '操作类型', '操作数量', '操作说明', '操作人', '操作时间'];
                foreach ($model->records as $record) {
                    $type = '入库';
                    if($record->type === 0) {
                        $type= '出库';
                    }
                    $modelsData[] = ['', $type, $record->count, $record->remark, $record->operator->name, $record->updated_at];
                }
            }
            $modelsData[] = [];
        }
        $cabinetsData = [];
        $cabinets = Cabinet::with('records.operator')->get();
        foreach ($cabinets as $cabinet) {
            $cabinetsData[] = ['名称', '库存数量', '最近更新'];
            $cabinetsData[] = [$cabinet->name, $cabinet->count, $cabinet->updated_at];
            if(!$cabinet->records->isEmpty()) {
                $cabinetsData[] = ['', '操作类型', '操作数量', '操作说明', '操作人', '操作时间'];
                foreach ($cabinet->records as $record) {
                    $type = '入库';
                    if($record->type === 0) {
                        $type= '出库';
                    }
                    $cabinetsData[] = ['', $type, $record->count, $record->remark, $record->operator->name, $record->updated_at];
                }
            }
            $cabinetsData[] = [];
        }
        $fansData = [];
        $fans = Fan::with('records.operator')->get();
        foreach ($fans as $fan) {
            $fansData[] = ['名称', '库存数量', '最近更新'];
            $fansData[] = [$fan->name, $fan->count, $fan->updated_at];
            if(!$fan->records->isEmpty()) {
                $fansData[] = ['', '操作类型', '操作数量', '操作说明', '操作人', '操作时间'];
                foreach ($fan->records as $record) {
                    $type = '入库';
                    if($record->type === 0) {
                        $type= '出库';
                    }
                    $fansData[] = ['', $type, $record->count, $record->remark, $record->operator->name, $record->updated_at];
                }
            }
            $fansData[] = [];
        }
        $partsData = [];
        $parts = Part::with('records.operator')->get();
        foreach ($parts as $part) {
            $partsData[] = ['名称', '库存数量', '最近更新'];
            $partsData[] = [$part->name, $part->count, $part->updated_at];
            if(!$part->records->isEmpty()) {
                $partsData[] = ['', '操作类型', '操作数量', '操作说明', '操作人', '操作时间'];
                foreach ($part->records as $record) {
                    $type = '入库';
                    if($record->type === 0) {
                        $type= '出库';
                    }
                    $partsData[] = ['', $type, $record->count, $record->remark, $record->operator->name, $record->updated_at];
                }
            }
            $partsData[] = [];
        }
        Excel::create(iconv('UTF-8', 'GBK', '产品仓储明细'),
            function($excel) use ($modelsData, $cabinetsData, $fansData, $partsData){
            $excel->sheet('车型', function($sheet) use ($modelsData){
                $sheet->rows($modelsData);
            });
            $excel->sheet('柜体', function($sheet) use ($cabinetsData){
                $sheet->rows($cabinetsData);
            });
            $excel->sheet('风机', function($sheet) use ($fansData){
                $sheet->rows($fansData);
            });
            $excel->sheet('零件', function($sheet) use ($partsData){
                $sheet->rows($partsData);
            });
        })->export($fileType);
    }
}