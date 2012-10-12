<?php
function fsinf_add_event_page() {

?><h2>Erstelle ein neues Event</h2>

<div class="row">
<div class="alert alert-danger span4">Work in Progress here, folks</div>
</div>

<div class="row">
    <div class="span8">

<form action="" class="form-horizontal" method="POST">
    <div class="row">

        <div class="span4">
    <div class="control-group">
        <label class="control-label" for="inputTitle">Titel</label>
        <div class="controls">
            <input type="text" id="inputTitle" placeholder="z.B. Ersti-Hütte">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputBegin">Beginn</label>
        <div class="controls">
            <input type="text" id="inputBegin" placeholder="z.B. 31-12-2012">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputEnd">Ende</label>
        <div class="controls">
            <input type="text" id="inputEnd" placeholder="z.B. 30-12-2012">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputPlace">Ort</label>
        <div class="controls">
            <input type="text" id="inputPlace" placeholder="z.B. Schweiz">
        </div>
    </div>
</div>
<div class="span4">
    <div class="control-group">
        <label class="control-label" for="inputType">Art</label>
        <div class="controls">
            <select id="inputType">
                <option value="0">Hütte</option>
                <option value="1">Zelten</option>
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputParticipants">Teilnehmer</label>
        <div class="controls">
            <input type="number" id="inputParticipants" placeholder="z.B. 30">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputFee">Gebühr</label>
        <div class="controls">
            <input type="number" min="0" step="0.01" size="4" id="inputFee" placeholder="z.B. 49,05">
        </div>
    </div>
    </div>
    </div>
        <div class="control-group">
        <label class="control-label" for="inputDesc">Kurz-Beschreibung</label>
        <div class="controls">
            <textarea id="inputDesc" placeholder="z.B. Mehr kann man dann im Artikel schreiben" rows="4" cols="40"></textarea>
        </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Event erstellen</button>
    </div>
    </div>
</form>
</div>
</div>

<?php

}