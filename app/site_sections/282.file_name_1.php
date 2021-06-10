<div id="request_form" class="form-calculator">
    
<h3>��� ��������������</h3>
<div class="form-calculator-type">
<label class="form-calculator-type-label"><input class="form-calculator-type__input" type="radio" name="request_form_heat_exchanger_type" value="������������������ �������" checked="checked" />������������������ �������</label>
<label class="form-calculator-type-label"><input class="form-calculator-type__input" type="radio" name="request_form_heat_exchanger_type" value="����������������� �������" />����������������� �������</label>
<label class="form-calculator-type-label"><input class="form-calculator-type__input" type="radio" name="request_form_heat_exchanger_type" value="����������������" />����������������</label>
<label class="form-calculator-type-label"><input class="form-calculator-type__input" type="radio" name="request_form_heat_exchanger_type" value="��������� ����������" />��������� ����������</label>
</div>
    
<br />

<h3>������ �� ���� � �������</h3>
<h4>������:</h4>
<div class="form-calculator-water-data">
<input id="request_form_air_spending" type="text" class="form-control form-calculator-water-data__input" placeholder="�������, �3/�" value="" />
<input id="request_form_coolant_spending" type="text" class="form-control form-calculator-water-data__input" placeholder="�������������, �3/�" value="" />
</div>

<h4>����������� �������:</h4>
<div class="form-calculator-water-data">
<input id="request_form_input_air_temperature" type="text" class="form-control form-calculator-water-data__input" placeholder="�� �����, �C" value="" />
<input id="request_form_output_air_temperature" type="text" class="form-control form-calculator-water-data__input" placeholder="�� ������, �C" value="" />
</div>

<h4>����������� ������������� (���������� ������ ��� �������� ����������� � �������� ����������):</h4>
<div class="form-calculator-water-data">
<input id="request_form_input_coolant_temperature" type="text" class="form-control form-calculator-water-data__input" placeholder="�� �����, �C" value="" />
<input id="request_form_output_coolant_temperature" type="text" class="form-control form-calculator-water-data__input" placeholder="�� ������, �C" value="" />
</div>
    
<h4>��������� �������� (�������������, ���� ���� �<sub>�� ����</sub> � �<sub>��� ����</sub>):</h4>
<input id="request_form_power" type="text" class="form-control" placeholder="���" value="" />
<br />

<h3>������ �� ��������</h3>
<img src="/public/images/shema.jpg" class="img-responsive" ><br /><br />
<div class="form-calculator-sizes-data">
<input id="request_form_fta_length" type="text" class="form-control form-calculator-sizes-data__input" placeholder="����� FTA" value="" />
<input id="request_form_ftb_height" type="text" class="form-control form-calculator-sizes-data__input" placeholder="������ FTB" value="" />
<input id="request_form_s_width" type="text" class="form-control form-calculator-sizes-data__input" placeholder="������ S" value="" />
</div>

<h4>������� ���������� ���������:</h4>
<div class="form-calculator-diametr">
<div class="form-calculator-diametr-col">
<h5 class="form-calculator-diametr-col__h5">�� ����� C</h5>
<select id="request_form_input_s_diameter" class="form-control form-calculator-diametr-col__select">
<option value="DN 15 (1/2 �����)">DN 15 &nbsp;&nbsp;(1/2 �����)</option>
<option value="DN 20 (3/4 �����)">DN 20 &nbsp;&nbsp;(3/4 �����)</option>
<option value="DN 25 (1 ����)">DN 25 &nbsp;&nbsp;(1 ����)</option>
<option value="DN 32 (1 1/4 �����)">DN 32 &nbsp;&nbsp;(1 1/4 �����)</option>
<option value="DN 40 (1 1/2 �����)">DN 40 &nbsp;&nbsp;(1 1/2 �����)</option>
<option value="DN 50 (2 �����)">DN 50 &nbsp;&nbsp;(2 �����)</option>
<option value="DN 65 (2 1/2 �����)">DN 65 &nbsp;&nbsp;(2 1/2 �����)</option>
<option value="DN 80 (3 �����)">DN 80 &nbsp;&nbsp;(3 �����)</option>
<option value="DN 100 (4 �����)">DN 100 (4 �����)</option>
</select>
</div>

<div class="form-calculator-diametr-col">
<h5 class="form-calculator-diametr-col__h5">�� ������ C</h5>
<select id="request_form_output_s_diameter" class="form-control form-calculator-diametr-col__select">
<option value="DN 15 (1/2 �����)">DN 15 &nbsp;&nbsp;(1/2 �����)</option>
<option value="DN 20 (3/4 �����)">DN 20 &nbsp;&nbsp;(3/4 �����)</option>
<option value="DN 25 (1 ����)">DN 25 &nbsp;&nbsp;(1 ����)</option>
<option value="DN 32 (1 1/4 �����)">DN 32 &nbsp;&nbsp;(1 1/4 �����)</option>
<option value="DN 40 (1 1/2 �����)">DN 40 &nbsp;&nbsp;(1 1/2 �����)</option>
<option value="DN 50 (2 �����)">DN 50 &nbsp;&nbsp;(2 �����)</option>
<option value="DN 65 (2 1/2 �����)">DN 65 &nbsp;&nbsp;(2 1/2 �����)</option>
<option value="DN 80 (3 �����)">DN 80 &nbsp;&nbsp;(3 �����)</option>
<option value="DN 100 (4 �����)">DN 100 (4 �����)</option>
</select>
</div>

<div class="form-calculator-diametr-col">
<label class="form-calculator-diametr-col__label pointer"><input class="form-calculator-diametr-col__input pointer" id="request_form_unit" type="checkbox" name="type" checked="checked" /> ���� ������������� (�������)</label>
</div>
</div>

<h3>���������� ������</h3>
<div class="form-calculator-contact-data">
<input id="request_form_name" type="text" class="form-control form-calculator-contact-data__input" placeholder="���" value="" />
<input id="request_form_company" type="text" class="form-control form-calculator-contact-data__input" placeholder="��������" value="" />
<input id="request_form_city" type="text" class="form-control form-calculator-contact-data__input" placeholder="�����" value="" />
</div>
<div class="form-calculator-contact-data">
<input id="request_form_email" type="text" class="form-control form-calculator-contact-data__input" placeholder="E-mail" value="" />
<input id="request_form_phone" type="text" class="form-control form-calculator-contact-data__input" placeholder="�������" value="" />
</div>

<textarea id="request_form_notes" type="text" class="form-control form-calculator-contact-data__input" placeholder="����������"></textarea>
    
<button id="request_form_submit_button" type="button" class="green_btn form-calculator-contact-data__btn">���������</button>

<span id="request_form_ajax_preloader" class="ajax_preloader"></span>
    
</div>