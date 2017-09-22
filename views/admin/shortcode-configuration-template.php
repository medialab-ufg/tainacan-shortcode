<div class="wrap">
    <form>
        <div class="form-group">
            <h2>Items template</h2>
            <div>
                <h4>Informações dísponiveis</h4>
                <ul class="list-inline">
                    <li>Título: <strong>{title}</strong></li>
                    <li>Data do post: <strong>{date}</strong></li>
                    <li>Conteúdo/Descrição: <strong>{content}</strong></li>
                    <li>Data da ultima modificação: <strong>{last_modified}</strong></li>
                    <li>Link: <strong>{link}</strong></li>
                    <li>Quantidade de comentários: <strong>{comment_count}</strong></li>
                    <li>Link da miniatura: <strong>{thumbnail}</strong></li>
                    <li>Link da capa: <strong>{cover}</strong></li>
                </ul>
            </div>
            <textarea id="image-show-template"></textarea>
        </div>

        <div class="form-group">
            <h2>Collection template</h2>
            <textarea id="collection-show-template" class="form-control" rows="15"></textarea>
        </div>

        <button type="button" onclick="save_templates();" class="btn btn-primary btn-lg pull-right">Salvar</button>
    </form>

</div>