<?php

/**
 * Tato trida poskytuje informace o uzivatelich. Nejspise by byla bezne zavisla na pripojeni k databazi, aby mohla udaje nacist.
 */
class UserDetailProvider
{
    public static $users = [
        1 => ['name' => 'Admin', 'sex' => UserSex::MALE],
        2 => ['name' => 'Muž', 'sex' => UserSex::MALE],
        3 => ['name' => 'Žena', 'sex' => UserSex::FEMALE],
    ];

    public function getSexForId(int $id)
    {
        if (isset(self::$users[$id])) {
            return self::$users[$id]['sex'];
        }
        return UserSex::MALE;
    }

    public function getNameForId(int $id)
    {
        if (isset(self::$users[$id])) {
            return self::$users[$id]['name'];
        }
        return '???';
    }
}