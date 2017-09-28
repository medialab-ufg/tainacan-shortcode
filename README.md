# Tainacan shortcode

Tainacan shortcode é um plugin que possibilita o uso de shortcodes em seu site com o intuito exibir uma coleção e/ou um conjunto de itens que estejam em algum outro site em que o Tainacan esteja instalado.

## Como utilizar?
Há basicamente dois tipos de shortcode, para coleção e para itens de uma coleção. 

### Exibindo coleções
Para exibir uma coleção basta utilizar o seguinte código:

```sh
[tainacan-show-collection tainacan-url="{URL da home de sua instalação Tainacan}" collection-name="{Nome da coleção a exibir}"]
```
Note que os parâmetros **tainacan-url** e **collection-name** são obrigatórios.

### Exibindo itens

Para exibir os itens pertencentes a uma determinada coleção utilize o seguinte código:

```sh
[tainacan-show-items tainacan-url="{URL da home de sua instalação Tainacan}" collection-name="{Nome da coleção dos itens}"]
```
Assim como na exibição de uma coleção os parâmetros **tainacan-url** e **collection-name** são obrigatórios.

#### Exibindo itens filtrados
Além de exibir todos os itens de uma coleção também é possível exibir um determinado conjunto e itens de uma coleção com base no valor de seus metadados.
```sh
[tainacan-show-items tainacan-url="{URL da home de sua instalação Tainacan}" collection-name="{Nome da coleção dos itens}" meta-name="{Nome do metadado}" meta-value="{Valor desejado para o metadado}"]
```
Os parâmetros **meta-name** e **meta-value** correspondem ao nome do metadado e ao valor desejado para aquele metadado respectivamente.

### Cache
Por padrão o é realizado o cache do id das coleções para melhorar o desempenho das consultas mas além deste cache pode ser realizado também o cache dos metadados de uma coleção e o dos metadados de itens.
#### Cache de coleção
Para realizar o cache de coleção basta adiciona o parâmetros **enable-cache="true"** como no exemplo abaixo: 
```sh
[tainacan-show-collection tainacan-url="{URL da home de sua instalação Tainacan}" collection-name="{Nome da coleção a exibir}" enable-cache="true"]
```
O tempo padrão antes que o cache seja renovado é de 24 horas, caso deseje mudar esse tempo então utilize o seguinte parâmetro **cache-time**. O tempo de cache é dado em horas, caso desejo tempo fracionado basta utilizar '.' para fracionar o tempo, exemplo:

```sh
[tainacan-show-collection tainacan-url="{URL da home de sua instalação Tainacan}" collection-name="{Nome da coleção a exibir}" enable-cache="true" cache-time="0.5"]
```

Neste exemplo as informações em cache terão validade de 30 minutos. É possível utilizar apenas **cache-time** para ativar o cache, desde que o tempo seja diferente do tempo padrão de cache que é de 24 horas.

### Cache de itens
O cache de itens funciona extamente igual ao cache de coleção. Exemplo:
```sh
[tainacan-show-items tainacan-url="{URL da home de sua instalação Tainacan}" collection-name="{Nome da coleção dos itens}" meta-name="{Nome do metadado}" meta-value="{Valor desejado para o metadado}" enable-cache="true" cache-time="12"]
```
## Exibição dos itens
Tanto a coleção quanto os itens são exibidos utilizando um template padrão, caso deseje alterar esse template é necessário criar um novo template e o cadastrar. Para cadastrar um novo template de exibição é necessário acessar o painel de administração do Wordpress, acessar o menu de configurações e clicar no menu "Tainacan shortcode". Nessa tela são mostradas duas caixa de edição, a primeira para o template de itens e a segunda para o template de coleção.

No template de itens deve ser colocado o código HTML para a exibição de apenas um item, esse código será replicado para todos os itens da resposta.
### Metadados disponíveis no template
Há oito metadados disponíveis para uso no template, cada metadado possui seu shortcode para ser utilizado no template. São eles:

| Metadado      | Shortcode     |
| ------------- |:-------------:|
| Título | {title} |
| Data do post | {date} |
| Conteúdo/Descrição | {content} |
| Data da última modificação | {last_modified} |
| Link | {link} |
| {Quantidade de comentários} | {comment_count} |
| Link da miniatura | {thumbnail} |
| Link da capa (disponível apenas em coleção) | {cover} |

Exempo template itens:
```sh
<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
  <a href="{link}" target="_blank" class="thumbnail">
    <img src="{thumbnail}">
    <div class="caption text-center">
      <h3>{title}</h3>
      <h4>{last_modified}</h4>
    </div>
  </a>
</div>
```

Exemplo template coleção:
```sh
<a href="{link}" target="_blank">
  <div id="tainacan-collection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-2">
          <img src="{thumbnail}" class="img-responsive">
        </div>

        <div class="col-md-10">
          <h1 id="post-title">{title}</h1>
          <h6>{last_modified}</h6>
          <p id="post-content">
            {content}
          </p>
        </div>
      </div>
    </div>
  </div>
</a>
```
