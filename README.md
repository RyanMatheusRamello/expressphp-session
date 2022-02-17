# expressphp-session
Gerenciador de sessão para ExpressPHP

## Usar no ExpressPHP

```php
$app->use(ExpressPHP\Plugins\Session::run(["secret" => "your_secret_key"]);
```

Adicione a função run na rota do express na qual você quer que a sessão seja interpretada

A sessão só será criada caso alguma variavel for adicionada

### Adicionar na sessão

```php
$req->session->set(string $name, mixed $value) : bool;
```

### Recuperar da sessão

```php
$req->session->get(?string $name) : mixed;
```

Caso não seja informado nada ele retorna todas as sessões

### Deletar varíavel da sessão

```php
$req->session->unset($name) : bool|null;
```

### Opções da função run

* `genid` (opcional)

Função para gerar uma id de sessão

* `secret` (required)

String que será usada para gerar uma id de sessão

* `cookie` (opcional)

Array que será usada na função session_set_cookie_params;

* `name` (opcional)

Nome da sessão, por padrão é express_session
