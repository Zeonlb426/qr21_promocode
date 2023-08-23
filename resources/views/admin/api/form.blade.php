<style>
    @media (min-width: 1200px) {
        .combo {
            padding-right: 5rem;
        }
    }
    @media (max-width: 992px) {
        .combo {
            grid-template-columns: 1fr;
            grid-template-areas:
                'preview'
                'form';
        }
    }

</style>
<div class="box box-info">
    <div class="box-header">
        <h3 class="box-title">{{ $form->title() }}</h3>
        <div class="box-tools">
            {!! $form->renderTools() !!}
        </div>
    </div>
    <!-- /.box-header -->
    <div class="combo">
        <!-- form start -->
        {!! $form->open() !!}

        <div class="box-body">

            @if(!$tabObj->isEmpty())
                @include('admin::form.tab', compact('tabObj'))
            @else
                <div class="fields-group">
                    @if($form->hasRows())
                        @foreach($form->getRows() as $row)
                            {!! $row->render() !!}
                        @endforeach
                    @else
                        @foreach($layout->columns() as $column)
                            <div class="col-md-12">
                                @foreach($column->fields() as $field)
                                    {!! $field->render() !!}
                                @endforeach
                                <div class="form-group  ">
                                    <label for="login" class="col-sm-2"></label>
                                    <div class="col-sm-8">
                                        <div class="btn-group pull-left">
                                            <div id="generation" class="btn btn-primary">Сгенерировать сложный пароль</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endif
        </div>
        <!-- /.box-body -->
        {!! $form->renderFooter() !!}
        @foreach($form->getHiddenFields() as $field)
            {!! $field->render() !!}
        @endforeach
        <!-- /.box-footer -->
        {!! $form->close() !!}
    </div>
</div>
<script>
    const generation = $('#generation');
    const inputGen = $('#password');
    generation.on('click keydown', function(e){
        inputGen[0].value = pass_gen(16);
    });
    function pass_gen(len) {
        chrs = 'abd!ehkmnpswxzA*BDEFGHKMN_PQRSTWXZ123456789';
        var str = '';
        for (var i = 0; i < len; i++) {
            var pos = Math.floor(Math.random() * chrs.length);
            str += chrs.substring(pos,pos+1);
        }
        return str;
    }
</script>
