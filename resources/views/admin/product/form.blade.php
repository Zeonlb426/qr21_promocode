<style>
    .col-sm-8 {
        width: 80% !important;
    }
    .box-body {
        padding-top: 1.5rem;
        padding-right: 0;
        padding-bottom: 1.5rem;
        padding-left: 0;
    }
    .combo {
        display: grid;
        grid-template-columns: 1fr 300px;
        padding: 1.5rem;
        grid-template-areas:
                'form preview';
    }
    .preview {
        padding: 1.5rem 0;
        grid-area: preview;
    }
    .form-horizontal {
        grid-area: form;
    }
    .gift {
        background: #FAF7F2;
        border: 2px solid #E6E6E6;
        box-sizing: border-box;
        border-radius: 16px;
        display: grid;
        grid-template-columns: 3fr 1fr;
        padding: 10px;
        box-shadow: 0px 2px 4px 0px #0000003d, 0px 3px 6px #c9c67147;
        min-height: 134px;
    }
    .gift-img {
        max-width: 69px;
        max-height: 100px;
        margin-top: 20px;
    }
    .gift-text {
        font-style: normal;
        font-weight: 500;
        font-size: 16px;
        line-height: 100%;
        letter-spacing: -0.03em;
        color: #000000;
        margin-bottom: 10px;
        max-width: 200px;
    }
    .gift-label {
        font-weight: 600;
        font-size: 10px;
        line-height: 24px;
        display: inline-block;
        text-align: center;
        letter-spacing: -0.03em;
        background: #333649;
        border-radius: 8px;
        color: #FFFFFF;
        padding: 0 6px;
        margin-bottom: 10px;
    }
     .gift-subtext {
         font-style: normal;
         font-weight: 500;
         font-size: 10px;
         line-height: 12px;
         letter-spacing: -0.03em;
         max-width: 90%;
         color: #939393;
         margin-bottom: 10px;
         min-height: 36px;
     }
    .gift-button {
        font-style: normal;
        margin-left: -10px;
        margin-bottom: -10px;
        padding: 8px;
        max-width: 70%;
        text-align: center;
        font-weight: 600;
        font-size: 12px;
        line-height: 100%;
        letter-spacing: -0.03em;
        color: #333649;
        background: #EDAD88;
        border-radius: 0px 14px 0px 14px;

    }
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
        .gift {
            max-width: 300px;
        }
        .preview {
            margin: 0 auto;
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
{{--        {!! dd($layout->columns()[0]->fields()[2]) !!}--}}
        <div class="preview">
            <div class="gift" @if ('off' == $layout->columns()[0]->fields()[2]->value()) style="filter: grayscale(1);opacity: 0.6;" @endif>
                <div>
{{--                    <div class="gift-text">Набор Ploom X:<br>устройство и стики</div>--}}
                    <div class="gift-text">{!! $layout->columns()[0]->fields()[3]->value() !!}</div>
                    <div style="min-height: 30px;">
                        <div class="gift-label">{!! $layout->columns()[0]->fields()[4]->value() !!}</div>
                    </div>
                    <div class="gift-subtext">{!! $layout->columns()[0]->fields()[5]->value() !!}</div>
                    <div class="gift-button">Специальная цена</div>
                </div>
                <img class="gift-img" src="">

            </div>
        </div>

    </div>
</div>
<script>
    const gift = $('.preview .gift');
    $(document).on('change', 'input[name="status"]', function(e) {
        if ('on' == e.target.value) {
            gift.css({'filter': 'none', 'opacity': 1});
        } else {
            gift.css({'filter': 'grayscale(1)', 'opacity': 0.6});
        }
    });
    const giftImage = $('.gift-img');
    $(document).ready(function () {
        const giftText = $('.gift-text');
        if ($("#title")[0].value.length == 0) { giftText.text('Заполните "Заголовок"'); }
        setImg(0);
    });
    $(document).on('change', 'input[name="image"]', function(e) {
        setTimeout(function(){
            let dataSrc = $('.file-preview-image')[0].src;
            setImg(dataSrc);
        }, 500);

    });

    const giftText = $('.gift-text');
    const giftLabel = $('.gift-label');
    const giftSubtext = $('.gift-subtext');

    $("#title").on('change keydown paste input', function(e){
        giftText.text(e.target.value);
        if (e.target.value.length > 60) { e.target.value = e.target.value.substring(0, 100); }
        if (e.target.value.length == 0) { giftText.text('Заполните "Заголовок"'); }
    });

    $("#label").on('change keydown paste input', function(e){
        giftLabel.text(e.target.value);
        if (e.target.value.length > 27) { e.target.value = e.target.value.substring(0, 27); }
    });

    $("#sub_title").on('change keydown paste input', function(e){
        giftSubtext.text(e.target.value);
        if (e.target.value.length > 70) { e.target.value = e.target.value.substring(0, 100); }
    });

    function setImg (dataSrc) {
        const giftImg = $('.image');
        const src1 = giftImg.attr('data-initial-preview');
        giftImage.attr('src', src1);
        if(dataSrc !== 0) {
            giftImage.attr('src', dataSrc);
        }
    };
</script>
