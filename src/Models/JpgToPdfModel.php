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
 * @property string $link       - link to jpg file
 * @property int    $status     - 0 = not converted, 1 = converted, 2 = is premium required
 * @property int    $transaction_id
 * @property string $created_at - Table created at
 * @property string $updated_at - Table updated at
 *
 */
class JpgToPdfModel extends BaseModel
{
    public const STATUS_DEACTIVATE = 0;
    protected $table    = 'jpg_to_pdf_files';
    protected $fillable = [
        'id',
        'user_id',
        'transaction_id',
        'link',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

}