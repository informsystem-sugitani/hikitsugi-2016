<?php

/**
 * Class���������Ƥ��ʤ����ˡ��ե������õ�����饹
 */
class ClassLoader
{
    // class �ե����뤬����ǥ��쥯�ȥ�Υꥹ��
    private static $dirs;

    /**
     * ���饹�����Ĥ���ʤ��ä����ƤӽФ����᥽�å�
     * spl_autoload_register �Ǥ��Υ᥽�åɤ���Ͽ���Ƥ�������
     * @param  string $class ̾�����֤ʤɴޤ�����饹̾
     * @return bool ���������true
     */
    public static function loadClass($class)
    {
        foreach (self::directories() as $directory) {
            // ̾�����֤䵿��̾�����֤򤳤��ǥѡ�������
            // Ŭ�ڤʥե�����ѥ��ˤ��Ƥ�������
            $file_name = "{$directory}/{$class}.php";

            if (is_file($file_name)) {
                require $file_name;

                return true;
            }
        }
    }

    /**
     * �ǥ��쥯�ȥ�ꥹ��
     * @return array �ե�ѥ��Υꥹ��
     */
    private static function directories()
    {
        if (empty(self::$dirs)) {
            $base = $_SERVER['DOCUMENT_ROOT'].BASE_PATH;
            self::$dirs = array(
                // �ڽ��סۤ������ɤ߹���Ǥۤ����ǥ��쥯�ȥ��­���Ƥ����ޤ�
                $base . '/app/classes',
            );
        }

        return self::$dirs;
    }
}

// �����¹Ԥ��ʤ��ȥ����ȥ������Ȥ���ư���ʤ�
spl_autoload_register(array('ClassLoader', 'loadClass'));
?>