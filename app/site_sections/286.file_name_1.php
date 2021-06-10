<p class="evaporator_desc">Обязательные данные — это ваша контактная информация.</p>
<p class="evaporator_desc">Технические характеристики нужны, чтобы рассчитать рядность, шаг между ламелями, оптимальный диаметр коллектора. Если вы точно знаете, какое оборудование необходимо — заполняйте все поля как можно подробнее.</p>

<p class="water_heater_desc">В ряде случаев мы предлагаем несколько вариантов, которые отличаются по стоимости. Для этого также нужны детальные характеристики рабочего режима.</p>
<p class="water_heater_desc">Если у вас есть вопросы, уточнения, сомнения, лучше обсудить все до мелочей с нашим специалистом.</p>

<div id="evaporator_form" class="form-calculator">
<h3>Данные по размерам</h3>
<img src="/public/images/isparitel.jpg" class="img-responsive" /><br /><br />

<div class="form-calculator-sizes-data">
<input id="evaporator_form_fta" type="text" class="form-control form-calculator-sizes-data__input" placeholder="FTA, мм" value="" />
<input id="evaporator_form_ftb" type="text" class="form-control form-calculator-sizes-data__input" placeholder="FTB, мм" value="" />
<input id="evaporator_form_a" type="text" class="form-control form-calculator-sizes-data__input" placeholder="A, мм" value="" />
</div>

<div class="form-calculator-sizes-data">
<input id="evaporator_form_b" type="text" class="form-control form-calculator-sizes-data__input" placeholder="B, мм" value="" />
<input id="evaporator_form_s" type="text" class="form-control form-calculator-sizes-data__input" placeholder="S, мм" value="" />
<input id="evaporator_form_e_diameter" type="text" class="form-control form-calculator-sizes-data__input" placeholder="Диаметр E, мм" value="" />
</div>

<div class="form-calculator-sizes-data">
<input id="evaporator_form_input_u_diameter" type="text" class="form-control form-calculator-sizes-data__input" placeholder="Диаметр U, мм" value="" />
<input id="evaporator_form_lane" type="text" class="form-control form-calculator-sizes-data__input" placeholder="Рядность" value="" />
</div>

<div class="form-calculator-sizes-data">
<input id="evaporator_form_lamella_step" type="text" class="form-control form-calculator-sizes-data__input" placeholder="Шаг ламели, мм" value="" />
<select id="water_heater_tray" class="form-control form-calculator-diametr-col__select">
<option value="" disabled selected>Поддон и каплеуловитель</option>
<option value="Нет">Нет</option>
<option value="Да">Да</option>
</select>
</div>

<h3>Материалы</h3>
<div class="form-calculator-sizes-data">
<select id="evaporator_form_tube_material" class="form-control form-calculator-diametr-col__select">
<option value="" disabled selected>Материал трубки</option>
<option value="Медь">Медь</option>
<option value="Нержавеющая сталь">Нержавеющая сталь</option>
</select>
<select id="evaporator_form_lamella_material" class="form-control form-calculator-diametr-col__select">
<option value="" disabled selected>Материал ламелей</option>
<option value="Алюминий">Алюминий</option>
<option value="Нержавеющая сталь">Нержавеющая сталь</option>
<option value="Медь">Медь</option>
</select>
</div>

<h3>Техническое задание</h3>
<div class="form-calculator-sizes-data">
<input id="evaporator_form_air_spending" type="text" class="form-control form-calculator-water-data__input" placeholder="Расход воздуха, м3/ч" value="" />
<input id="evaporator_form_air_humidity" type="text" class="form-control form-calculator-water-data__input" placeholder="Влажность воздуха, %" value="" />
</div>

<div class="form-calculator-sizes-data">
<input id="evaporator_form_input_air_temperature" type="text" class="form-control form-calculator-water-data__input" placeholder="Температура воздуха на входе, °C" value="" />
<input id="evaporator_form_output_air_temperature" type="text" class="form-control form-calculator-water-data__input" placeholder="Температура воздуха на выходе, °C" value="" />
</div>

<div class="form-calculator-sizes-data">
<select id="evaporator_form_freon_type" class="form-control form-calculator-diametr-col__select">
<option value="" disabled selected>Тип фреона</option>
<option value="R134a">R134a</option>
<option value="R22">R22</option>
<option value="R290">R290</option>
<option value="R404A">R404A</option>
<option value="R407A">R407A</option>
<option value="R410A">R410A</option>
<option value="R507A">R507A</option>
</select>
<input id="evaporator_form_freon_evaporation_temperature" type="text" class="form-control form-calculator-water-data__input" placeholder="Температура испарения фреона, °C" value="" />
<input id="evaporator_form_power" type="text" class="form-control form-calculator-water-data__input" placeholder="Мощность, кВт" value="" />
</div>

<div class="form-calculator-sizes-data">
<textarea id="evaporator_form_notes" type="text" class="form-control form-calculator-contact-data__input" placeholder="Дополнительная информация"></textarea>
</div>

<h3>Контактные данные</h3>

<div class="form-calculator-contact-data">
<input id="evaporator_form_name" type="text" class="form-control form-calculator-contact-data__input" placeholder="ФИО" value="" />
<input id="evaporator_form_company" type="text" class="form-control form-calculator-contact-data__input" placeholder="Компания" value="" />
<input id="evaporator_form_city" type="text" class="form-control form-calculator-contact-data__input" placeholder="Город" value="" />
</div>

<div class="form-calculator-contact-data">
<input id="evaporator_form_email" type="text" class="form-control form-calculator-contact-data__input" placeholder="E-mail" value="" />
<input id="evaporator_form_phone" type="text" class="form-control form-calculator-contact-data__input" placeholder="Телефон" value="" />
</div>

<button id="evaporator_form_submit_button" type="button" class="green_btn form-calculator-contact-data__btn">Отправить</button>

<span id="evaporator_form_ajax_preloader" class="ajax_preloader"></span>
    
</div>