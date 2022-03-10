<?php

declare (strict_types=1);

namespace App\Application\Websocket\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Utils\Codec\Json;

/**
 * @property int            $id
 * @property string         $msg_id
 * @property string         $type
 * @property string         $user_target
 * @property string         $room
 * @property mixed          $msg_content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class WebsocketMsg extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'websocket_msg';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['msg_id', 'user_target', 'room', 'msg_content', 'type'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    protected $appends = ['avatar'];

    //TODO 获取用户头像，根据实际业务获取
    public function getAvatarAttribute(): string
    {
        return 'https://s1.ax1x.com/2020/09/21/wb18PJ.png';
    }

    public function setMsgContentAttribute($value): void
    {
        $this->attributes['msg_content'] = is_array($value) ? Json::encode($value) : $value;
    }

//    public function getMsgContentAttribute($value)
//    {
//        try {
//            if (Json::decode($value, true)) {
//                return Json::decode($value, true);
//            }
//
//            return $value;
//        } catch (\Exception $exception) {
//            return $value;
//        }
//    }
}