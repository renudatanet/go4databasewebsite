{{-- A job category's page fields, shared by the add form and the edit
     modal ($prefix tells their ids apart). --}}
<div class="form-group">
    <label for="{{$prefix}}slug">{{__('Page Address (Slug)')}}</label>
    <input type="text" class="form-control" id="{{$prefix}}slug" name="slug" placeholder="{{__('e.g. technology-and-ai')}}">
    <small class="text-muted">{{__('The category page is /career/ followed by this. Leave empty to make it from the name. Changing it breaks links to the old address.')}}</small>
</div>
<div class="form-group">
    <label for="{{$prefix}}meta_description">{{__('Meta Description')}}</label>
    <textarea class="form-control" rows="2" id="{{$prefix}}meta_description" name="meta_description" maxlength="500"></textarea>
    <small class="text-muted">{{__('Shown in Google results and under the heading of the category page.')}}</small>
</div>
<div class="form-group">
    <label for="{{$prefix}}meta_tags">{{__('Page Title (SEO)')}}</label>
    <input type="text" class="form-control" id="{{$prefix}}meta_tags" name="meta_tags" maxlength="191">
    <small class="text-muted">{{__('The browser tab and Google title. Leave empty to use the category name.')}}</small>
</div>
