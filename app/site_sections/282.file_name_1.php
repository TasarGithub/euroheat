<div id="request_form" class="form-calculator">

    

<h3>Тип теплообменника</h3>

<div class="form-calculator-type">

<label class="form-calculator-type-label"><input class="form-calculator-type__input" type="radio" name="request_form_heat_exchanger_type" value="Воздухонагреватель водяной" checked="checked" />воздухонагреватель водяной</label>

<label class="form-calculator-type-label"><input class="form-calculator-type__input" type="radio" name="request_form_heat_exchanger_type" value="Воздухоохладитель водяной" />воздухоохладитель водяной</label>

<label class="form-calculator-type-label"><input class="form-calculator-type__input" type="radio" name="request_form_heat_exchanger_type" value="Электрокалорифер" />электрокалорифер</label>

<label class="form-calculator-type-label"><input class="form-calculator-type__input" type="radio" name="request_form_heat_exchanger_type" value="Фреоновый испаритель" />фреоновый испаритель</label>

</div>

    

<br />



<h3>Данные по воде и воздуху</h3>

<h4>Расход:</h4>

<div class="form-calculator-water-data">

<input id="request_form_air_spending" type="text" class="form-control form-calculator-water-data__input" placeholder="Воздуха, м3/ч" value="" />

<input id="request_form_coolant_spending" type="text" class="form-control form-calculator-water-data__input" placeholder="Теплоносителя, м3/ч" value="" />

</div>



<h4>Температура воздуха:</h4>

<div class="form-calculator-water-data">

<input id="request_form_input_air_temperature" type="text" class="form-control form-calculator-water-data__input" placeholder="На входе, °C" value="" />

<input id="request_form_output_air_temperature" type="text" class="form-control form-calculator-water-data__input" placeholder="На выходе, °C" value="" />

</div>



<h4>Температура теплоносителя (необходима только для водяного нагревателя и водяного охладителя):</h4>

<div class="form-calculator-water-data">

<input id="request_form_input_coolant_temperature" type="text" class="form-control form-calculator-water-data__input" placeholder="На входе, °C" value="" />

<input id="request_form_output_coolant_temperature" type="text" class="form-control form-calculator-water-data__input" placeholder="На выходе, °C" value="" />

</div>

    

<h4>Требуемая мощность (необязательно, если есть Т<sub>вх возд</sub> и Т<sub>вых возд</sub>):</h4>

<input id="request_form_power" type="text" class="form-control" placeholder="кВт" value="" />

<br />



<h3>Данные по размерам</h3>

<img src="/public/images/shema.jpg" class="img-responsive" ><br /><br />

<div class="form-calculator-sizes-data">

<input id="request_form_fta_length" type="text" class="form-control form-calculator-sizes-data__input" placeholder="Длина FTA" value="" />

<input id="request_form_ftb_height" type="text" class="form-control form-calculator-sizes-data__input" placeholder="Высота FTB" value="" />

<input id="request_form_s_width" type="text" class="form-control form-calculator-sizes-data__input" placeholder="Ширина S" value="" />

</div>



<h4>Диаметр подводящих патрубков:</h4>

<div class="form-calculator-diametr">

<div class="form-calculator-diametr-col">

<h5 class="form-calculator-diametr-col__h5">На входе C</h5>

<select id="request_form_input_s_diameter" class="form-control form-calculator-diametr-col__select">

<option value="DN 15 (1/2 дюйма)">DN 15 &nbsp;&nbsp;(1/2 дюйма)</option>

<option value="DN 20 (3/4 дюйма)">DN 20 &nbsp;&nbsp;(3/4 дюйма)</option>

<option value="DN 25 (1 дюйм)">DN 25 &nbsp;&nbsp;(1 дюйм)</option>

<option value="DN 32 (1 1/4 дюйма)">DN 32 &nbsp;&nbsp;(1 1/4 дюйма)</option>

<option value="DN 40 (1 1/2 дюйма)">DN 40 &nbsp;&nbsp;(1 1/2 дюйма)</option>

<option value="DN 50 (2 дюйма)">DN 50 &nbsp;&nbsp;(2 дюйма)</option>

<option value="DN 65 (2 1/2 дюйма)">DN 65 &nbsp;&nbsp;(2 1/2 дюйма)</option>

<option value="DN 80 (3 дюйма)">DN 80 &nbsp;&nbsp;(3 дюйма)</option>

<option value="DN 100 (4 дюйма)">DN 100 (4 дюйма)</option>

</select>

</div>



<div class="form-calculator-diametr-col">

<h5 class="form-calculator-diametr-col__h5">На выходе C</h5>

<select id="request_form_output_s_diameter" class="form-control form-calculator-diametr-col__select">

<option value="DN 15 (1/2 дюйма)">DN 15 &nbsp;&nbsp;(1/2 дюйма)</option>

<option value="DN 20 (3/4 дюйма)">DN 20 &nbsp;&nbsp;(3/4 дюйма)</option>

<option value="DN 25 (1 дюйм)">DN 25 &nbsp;&nbsp;(1 дюйм)</option>

<option value="DN 32 (1 1/4 дюйма)">DN 32 &nbsp;&nbsp;(1 1/4 дюйма)</option>

<option value="DN 40 (1 1/2 дюйма)">DN 40 &nbsp;&nbsp;(1 1/2 дюйма)</option>

<option value="DN 50 (2 дюйма)">DN 50 &nbsp;&nbsp;(2 дюйма)</option>

<option value="DN 65 (2 1/2 дюйма)">DN 65 &nbsp;&nbsp;(2 1/2 дюйма)</option>

<option value="DN 80 (3 дюйма)">DN 80 &nbsp;&nbsp;(3 дюйма)</option>

<option value="DN 100 (4 дюйма)">DN 100 (4 дюйма)</option>

</select>

</div>



<div class="form-calculator-diametr-col">

<label class="form-calculator-diametr-col__label pointer"><input class="form-calculator-diametr-col__input pointer" id="request_form_unit" type="checkbox" name="type" checked="checked" /> Узел регулирования (типовой)</label>

</div>

</div>



<h3>Контактные данные</h3>

<div class="form-calculator-contact-data">

<input id="request_form_name" type="text" class="form-control form-calculator-contact-data__input" placeholder="ФИО" value="" />

<input id="request_form_company" type="text" class="form-control form-calculator-contact-data__input" placeholder="Компания" value="" />

<input id="request_form_city" type="text" class="form-control form-calculator-contact-data__input" placeholder="Город" value="" />

</div>

<div class="form-calculator-contact-data">

<input id="request_form_email" type="text" class="form-control form-calculator-contact-data__input" placeholder="E-mail" value="" />

<input id="request_form_phone" type="text" class="form-control form-calculator-contact-data__input" placeholder="Телефон" value="" />

</div>



<textarea id="request_form_notes" type="text" class="form-control form-calculator-contact-data__input" placeholder="Примечания"></textarea>

    

<button id="request_form_submit_button" type="button" class="green_btn form-calculator-contact-data__btn">Отправить</button>



<span id="request_form_ajax_preloader" class="ajax_preloader"></span>

    

</div>