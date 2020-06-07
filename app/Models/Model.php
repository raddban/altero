<?php

namespace App\Models;
use App\View\Home;
use Medoo\Medoo;



class Model
{
    public static function model()
    {
        if (!empty($_POST))
        {
            if ($_POST['summ'] < 5000)
            {
                self::connect_db()->insert('applications', [
                    'user_email' => $_POST['email'],
                    'partner_email' => 'partnerB@example.org',
                    'summ' => $_POST['summ']
                ]);
            }elseif ($_POST['summ'] >= 5000)
            {
                self::connect_db()->insert('applications', [
                    'user_email' => $_POST['email'],
                    'partner_email' => 'partnerA@example.org',
                    'summ' => $_POST['summ']
                ]);
            }
        }
        self::insert_in_deals_table();
        self::send_email();
        return Home::view('index.php');
    }

    public static function insert_in_deals_table()
    {
        if (!empty($_POST))
        {
            if ( $_POST['summ'] < 5000 )
            {
                self::connect_db()->insert('deals', [
                    'summ' => $_POST['summ'],
                    'partner_email' => 'partnerA@example.org',
                    'status' => 'ask'
                ]);
            }else if( $_POST['summ'] >= 5000 )
            {
                self::connect_db()->insert('deals', [
                    'summ' => $_POST['summ'],
                    'partner_email' => 'partnerB@example.org',
                    'status' => 'ask'
                ]);
            }
        }
    }

    public static function connect_db()
    {
        return new Medoo([
            'database_type' => 'mysql',
            'database_name' => DB_NAME,
            'server' => 'localhost',
            'username' => USERNAME,
            'password' => PASSWORD
        ]);
    }

    public static function send_email()
    {
        $deal = self::connect_db()->select('deals', [
            '[><]applications' => 'summ'
        ],[
            'deals.status',
            'applications.user_email',
            'applications.summ',
            'deals.partner_email'
        ],[
            'status' => 'offer'
        ]);
        if ($deal != null)
        {
            if ($deal[0]['status'])
            {
                error_log("{$deal[0]['user_email']} You have an offer! Please contact us for more information. ", '3',
                    'log.txt');
                $delete = self::connect_db()->delete('deals', [
                    'status' => 'offer'
                ]);
                if ($delete)
                {
                    self::connect_db()->insert('email_after_offer', [
                        'user_email' => $deal[0]['user_email'],
                        'offer_summ' => $deal[0]['summ'],
                        'partner_email' => $deal[0]['partner_email']
                    ]);
                    header('location: /');
                }
            }
        }
    }
}