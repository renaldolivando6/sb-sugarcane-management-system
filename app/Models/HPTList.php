<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HPTList extends Model
{
    public $incrementing = false;
    protected $table = 'hptlst';
    protected $primaryKey = ['nosample', 'companycode', 'tanggaltanam', 'nourut'];
    public function setCreatedAt($value)
    {
        $this->attributes['createdat'] = $value;
    }

    public function getCreatedAtAttribute()
    {
        return $this->attributes['createdat'];
    }
    protected $fillable = [
        'nosample',
        'companycode',
        'tanggaltanam',
        'nourut',
        'jumlahbatang',
        'ppt',
        'pbt',
        'skor0',
        'skor1',
        'skor2',
        'skor3',
        'skor4',
        'per_ppt',
        'per_ppt_aktif',
        'per_pbt',
        'per_pbt_aktif',
        'sum_ni',
        'int_rusak',
        'telur_ppt',
        'larva_ppt1',
        'larva_ppt2',
        'larva_ppt3',
        'larva_ppt4',
        'pupa_ppt',
        'ngengat_ppt',
        'kosong_ppt',
        'telur_pbt',
        'larva_pbt1',
        'larva_pbt2',
        'larva_pbt3',
        'larva_pbt4',
        'pupa_pbt',
        'ngengat_pbt',
        'kosong_pbt',
        'dh',
        'dt',
        'kbp',
        'kbb',
        'kp',
        'cabuk',
        'belalang',
        'serang_grayak',
        'jum_grayak',
        'serang_smut',
        'smut_stadia1',
        'smut_stadia2',
        'smut_stadia3',
        'inputby',
        'createdat',
        'updatedat'
    ];
}
