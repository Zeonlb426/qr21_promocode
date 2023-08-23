<div {!! $attributes !!}>
    <div class="inner">
        <h3 style="font-size: 22px;">{{ $title }}</h3>
        <hr style="margin: 5px 0 10px 0;">
        <div style="display: grid;grid-template-columns: 200px 1fr;align-items: baseline;"><div style="color: {{ $free>999 ? '#00a65a' : ($free>299 ? '#f39c12' : '#dd4b39') }};">Свободных кодов:</div><p style="color: #fff;font-weight: 400;padding: 5px;background-color: {{ $free>999 ? '#00a65a' : ($free>299 ? '#f39c12' : '#dd4b39') }};border-radius: 6px;text-align: center;">{{ number_format($free, 0,'.',' ') }}</p></div>
        <div style="display: grid;grid-template-columns: 200px 1fr;align-items: baseline;"><div style="color: #c5c5c5;">Использованых кодов:</div><p style="color:#FFF;font-weight: 400;padding: 5px;font-size: 14px;background-color: #cbcbcb;border-radius: 6px;text-align: center;">{{ number_format($busy, 0,'.',' ') }}</p></div>
        <div style="display: grid;grid-template-columns: 200px 1fr;align-items: baseline;"><div >Всего загружено кодов:</div><p style="font-weight: 600;text-align: center;">{{ number_format($total, 0,'.',' ') }}</p></div>
        @if($type_promocode == 'bar')
            <div style="display: grid;grid-template-columns: 140px 1fr;">
                <div>Тип промо-кода:</div>
                <div style="display: grid;grid-template-columns: 20px 1fr;align-items: center;"><i class="fa fa-barcode" style="font-size: 20px;"></i><span style="margin-left: 5px;text-align: left;font-size: 12px;">- Штрих-код</span></div>
            </div>
        @elseif($type_promocode == 'qr')
            <div style="display: grid;grid-template-columns: 140px 1fr;">
                <div>Тип промо-кода:</div>
                <div style="display: grid;grid-template-columns: 20px 1fr;align-items: center;"><i class="fa fa-qrcode" style="font-size: 20px;"></i><span style="margin-left: 5px;text-align: left;font-size: 12px;">- QR-код</span></div>
            </div>
        @elseif($type_promocode == 'symbol')
            <div style="display: grid;grid-template-columns: 140px 1fr;">
                <div>Тип промо-кода:</div>
                <div style="display: grid;grid-template-columns: 42px 1fr;align-items: center;"><i class="fa">«ABC»</i><span style="margin-left: 5px;text-align: left;font-size: 12px;">- Символы</span></div>
            </div>
        @else
            <div style="display: grid;grid-template-columns: 140px 1fr;">
                <div>Тип промо-кода:</div>
                <div style="display: grid;grid-template-columns: 10px 1fr;align-items: center;"><i class="fa fa-mobile-phone" style="font-size: 20px;"></i><span style="margin-left: 5px;text-align: left;font-size: 12px;">- Показать экран</span></div>
            </div>
        @endif
        @if($send)
            <div style="display: grid;grid-template-columns: 170px 1fr">
                <div>Рассылка оповещения:</div>
                <div><span style="margin-left: 15px; background-color: #00a65a;color: #fff;padding: 0px 4px;border-radius: 6px;">Вкл.</span></div>
            </div>
        @else
            <div style="display: grid;grid-template-columns: 170px 1fr">
                <div>Рассылка оповещения:</div>
                <div><span style="margin-left: 15px; background-color: #dd4b39;color: #fff;padding: 0px 4px;border-radius: 6px;">Откл.</span></div>
            </div>
        @endif
    </div>
</div>
