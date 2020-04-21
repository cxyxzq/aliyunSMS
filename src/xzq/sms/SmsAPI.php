<?php
namespace xzq\sms;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 2020/4/21
 * Time: 17:30
 */

class SmsAPI
{
    private static $instance;

    private function __construct()
    {
        $accessKeyId = $this->getConfig('accessKeyId');
        $accessSecret = $this->getConfig('accessSecret');
        $this->init($accessKeyId,$accessSecret);
    }

    /**
     * 获取配置
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    private function getConfig($key){
        try{
            if (config($key)){
                return config($key);
            }
        }catch (\Exception $e){
            throw new \Exception($key.'配置不存在');
        }
    }


    private function init($accessKeyId,$accessSecret){
        AlibabaCloud::accessKeyClient($accessKeyId, $accessSecret)
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
        return $this;
    }

    /**
     *  获取实例
     */
    public static function getInstance(){
        if (!self::$instance instanceof self){
            self::$instance = new SmsAPI();
        }
        return self::$instance;
    }

    /**
     * 发送短信
     * @param $phone
     * @param $SignName
     * @param $TemplateCode
     * @param array $TemplateParam
     * @param string $SmsUpExtendCode
     * @param string $OutId
     */
    public function SendSms($phone,$SignName,$TemplateCode,$TemplateParam = [],$SmsUpExtendCode = '',$OutId = ''){
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'PhoneNumbers' => $phone,
                        'SignName' => $SignName,
                        'TemplateCode' => $TemplateCode,
                        'TemplateParam' => $TemplateParam,
                        'SmsUpExtendCode' => $SmsUpExtendCode,
                        'OutId' => $OutId,
                    ],
                ])
                ->request();
            print_r($result->toArray());
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }
}