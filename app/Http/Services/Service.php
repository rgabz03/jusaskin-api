<?php
namespace App\Http\Services;
use App\Cache;
use JWTAuth;

class Service
{


    public function generateRandomKey($limit=4) {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($permitted_chars), 0, $limit);
    }

    public function getCache($cid) {
		$now = time();
        $cache = Cache::where('cid',$cid)->where('expire',">=",$now)->first();
		if($cache !== null) {
		    return $cache;
		}
    }



    public function setCache($cacheId, $jobs, $expiry = '+2 minutes' ) {
        $cacheData = serialize($jobs);
        $cacheExpires = strtotime($expiry);
        $now = time();
        $cache = Cache::where('cid',$cacheId)->first();
        if($cache === null) {
            //updated
            $newCache = new Cache();
            $newCache->cid = $cacheId;
            $newCache->data = $cacheData;
            $newCache->expire = $cacheExpires;
            $newCache->created = $now;
            $newCache->save();
        }else{
            //updated
            Cache::where('cid',$cache->cid)->update([
                "cid" => $cacheId,
                "data" => $cacheData,
                "expire" => $cacheExpires,
                "created" => $now,
            ]);
        }
    }
}
