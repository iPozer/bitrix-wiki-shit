# Email #

**Q: Как прокинуть в шаблон письма тему(subject) пиьсма?**

Добавить event для письма
```
$manager->addEventHandler('main',   'OnBeforeMailSend',       [__CLASS__, 'beforeMailSend']);
```

```
    /**
     * Обработка всех писем: замена заголовка в шаблоне письма
     */
    public static function beforeMailSend($event) {
        $params = $event->getParameters();
        $params = reset($params);

        if ($params['BODY']) {
            $body = $params['BODY'];
            $params['BODY'] = str_replace('#SUBJECT#', $params['SUBJECT'], $body);
        }

        return $params;
    }
```