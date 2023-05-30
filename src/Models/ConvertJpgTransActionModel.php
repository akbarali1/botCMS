<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * Created by PhpStorm.
 * Filename: JpgToPdfModel.php
 * Project Name: jpgToPDF
 * Author: akbarali
 * Date: 26/03/2023
 * Time: 16:27
 * Github: https://github.com/akbarali1
 * Telegram: @akbar_aka
 * E-mail: me@akbarali.uz
 *
 * @mixin Builder
 *
 * @property int    $id         - Auto increment
 * @property int    $user_id    - user id related UserModel
 * @property string $created_at - Table created at
 * @property string $updated_at - Table updated at
 *
 */
class ConvertJpgTransActionModel extends BaseModel
{
    protected $table    = 'convert_jpg_transaction';
    protected $fillable = [
        'id',
        'user_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

}