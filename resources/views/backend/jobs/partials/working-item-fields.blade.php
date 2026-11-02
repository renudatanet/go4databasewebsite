{{-- The fields of one block of the Working at Go4Database page, for adding
     one ($item null) or editing it. $labels (CareerPageItem::SECTIONS) says
     what each field means in this section; a null label leaves it out. --}}
<div class="row">
    <div class="col-md-6 form-group">
        <label for="{{ $prefix }}-title">{{ __($labels['title']) }}</label>
        <input type="text" class="form-control" id="{{ $prefix }}-title" name="title" value="{{ optional($item)->title }}" required maxlength="191">
    </div>
    @if($labels['subtitle'])
        <div class="col-md-6 form-group">
            <label for="{{ $prefix }}-subtitle">{{ __($labels['subtitle']) }}</label>
            <input type="text" class="form-control" id="{{ $prefix }}-subtitle" name="subtitle" value="{{ optional($item)->subtitle }}" maxlength="191">
        </div>
    @endif
    <div class="col-12 form-group">
        <label for="{{ $prefix }}-body">{{ __($labels['body']) }}</label>
        <textarea class="form-control" rows="3" id="{{ $prefix }}-body" name="body" maxlength="1000" @if($section === 'quote') required @endif>{{ optional($item)->body }}</textarea>
    </div>
    @if($labels['link'])
        <div class="col-md-6 form-group">
            <label for="{{ $prefix }}-link">{{ __($labels['link']) }}</label>
            <input type="text" class="form-control" id="{{ $prefix }}-link" name="link" value="{{ optional($item)->link }}" maxlength="191" placeholder="/email-verifier">
            <small class="text-muted">{{ __('A page of this site, starting with /, or a full address starting with https://.') }}</small>
        </div>
    @endif
    @if($labels['filter'])
        <div class="col-md-6 form-group">
            <label for="{{ $prefix }}-filter">{{ __($labels['filter']) }}</label>
            <input type="text" class="form-control" id="{{ $prefix }}-filter" name="filter" value="{{ optional($item)->filter }}" maxlength="60" placeholder="Noida">
            <small class="text-muted">{{ __('Its “See open roles” link searches the roles for this word. Empty uses the city.') }}</small>
        </div>
    @endif
    <div class="col-md-3 form-group">
        <label for="{{ $prefix }}-order">{{ __('Order') }}</label>
        <input type="number" class="form-control" id="{{ $prefix }}-order" name="sr_order" value="{{ optional($item)->sr_order ?? 0 }}" min="0" max="999">
        <small class="text-muted">{{ __('Lower numbers come first.') }}</small>
    </div>
    <div class="col-md-3 form-group">
        <label for="{{ $prefix }}-status">{{ __('Status') }}</label>
        <select class="form-control" id="{{ $prefix }}-status" name="status">
            <option value="publish" @if(optional($item)->status !== 'draft') selected @endif>{{ __('Shown') }}</option>
            <option value="draft" @if(optional($item)->status === 'draft') selected @endif>{{ __('Hidden') }}</option>
        </select>
    </div>
    @if($labels['image'])
        @if($labels['cutout'])
            <div class="col-12 form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="{{ $prefix }}-cutout" name="cutout" value="1" @if(optional($item)->cutout) checked @endif>
                    <label class="custom-control-label" for="{{ $prefix }}-cutout">{{ __('Photo is a cut-out (transparent background)') }}</label>
                </div>
                <small class="text-muted">{{ __('A PNG with the background removed: the person then stands in front of the coloured circle, as on the card in the page header. Leave unticked for an ordinary photo, which is shown as a round portrait. Best: square, 800×800, cropped from the chest up.') }}</small>
            </div>
        @endif
        <div class="col-12">
            <x-media-upload :name="'image'" :id="optional($item)->image ?? ''" :title="__($labels['image'])" :dimentions="$section === 'quote' ? '800x800' : '1200x750'"/>
        </div>
    @endif
</div>
