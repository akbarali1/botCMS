<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder;

/**
 * Created by PhpStorm.
 * Filename: UserModel.php
 * Project Name: jpgToPDF
 * Author: akbarali
 * Date: 26/03/2023
 * Time: 16:22
 * Github: https://github.com/akbarali1
 * Telegram: @akbar_aka
 * E-mail: me@akbarali.uz
 *
 * @mixin Builder
 *
 * @property int                        $id          - Auto increment
 * @property int                        $today       - last active date
 * @property int                        $limit       - user one day limit used
 * @property int                        $telegram_id - user telegram id
 * @property int                        $condition   - 0 = not send, 1 = send
 * @property int                        $convert_id
 * @property bool                       $is_ban      - 0 = not ban, 1 = ban, default  not ban
 * @property bool                       $is_premium  - 0 = not premium, 1 = premium, default not premium
 * @property string                     $username    - Telegram username
 * @property string                     $time        - unix time
 * @property string                     $bot         - Start bot Username
 * @property string                     $created_at  - Table created time
 * @property string                     $updated_at  - Table updated time
 *
 * @property ConvertJpgTransActionModel $transaction
 */
class UserModel extends BaseModel
{
    protected $table    = 'users';
    protected $fillable = [
        'id',
        'today',
        'limit',
        'telegram_id',
        'convert_id',
        'condition',
        'username',
        'is_ban',
        'is_premium',
        'time',
        'bot',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'telegram_id' => 'integer',
        'condition'   => 'integer', // 0 - not send, 1 - send
        'is_ban'      => 'boolean', // 0 - not ban, 1 - ban, default 0
        'is_premium'  => 'boolean', // 0 - not premium, 1 - premium, default 0
    ];

    public function jpgToPdf(): HasMany
    {
        return $this->hasMany(JpgToPdfModel::class, 'user_id', 'id')->where('status', '=', 0);
    }

    public function jpgToPdfNoActive()
    {
        return $this->jpgToPdf()->where('status', '=', 0);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(ConvertJpgTransActionModel::class, 'id', 'convert_id');
    }

    public function isConvertActive(): bool
    {
        return $this->convert_id > 0;
    }

}