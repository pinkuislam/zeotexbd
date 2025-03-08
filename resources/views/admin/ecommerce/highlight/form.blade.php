<div class="row">
    <div class="col-sm-8">

        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
            <label class="control-label col-sm-2 required">Title:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="title" value="{{ old('title', isset($data) ? $data->title : '') }}" required>

                @if ($errors->has('title'))
                    <span class="help-block">
                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <div class="form-group{{ $errors->has('link') ? ' has-error' : '' }}">
            <label class="control-label col-sm-2 required">Link:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="link" value="{{ old('link', isset($data) ? $data->link : '') }}" required>

                @if ($errors->has('link'))
                    <span class="help-block">
                        <strong>{{ $errors->first('link') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <div class="form-group @error('image') has-error @enderror">
            <label class="control-label col-sm-2">Image:</label>
            <div class="col-sm-9">
            <x-sp-components::image-input id="image" name="image" path="{{ isset($data) ? MediaUploader::showUrl('highlights', $data->image) : null }}" />
            </div>
        </div>

        <div class="form-group @error('is_new_tab') has-error @enderror">
            <label class="control-label col-sm-3 required">Is New Tab:</label>
            <div class="col-sm-9">
                <select name="is_new_tab" class="form-control select2" required>
                    @php($is_new_tab = old('is_new_tab', isset($data) ? $data->is_new_tab : ''))
                    @foreach (['Yes', 'No'] as $sts)
                        <option value="{{ $sts }}" {{ $is_new_tab == $sts ? 'selected' : '' }}>{{ $sts }}</option>
                    @endforeach
                </select>

                @error('is_new_tab')
                    <span class="help-block">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="form-group @error('status') has-error @enderror">
            <label class="control-label col-sm-3 required">Status:</label>
            <div class="col-sm-9">
                <select name="status" class="form-control select2" required>
                    @php($status = old('status', isset($data) ? $data->status : ''))
                    @foreach (['Active', 'Deactivated'] as $sts)
                        <option value="{{ $sts }}" {{ $status == $sts ? 'selected' : '' }}>{{ $sts }}</option>
                    @endforeach
                </select>

                @error('status')
                    <span class="help-block">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="form-group text-center">
            <button type="submit" class="btn btn-success btn-flat btn-lg">{{ isset($data) ? 'Update' : 'Submit' }}</button>
            <button type="reset" class="btn btn-custom btn-flat btn-lg">Clear</button>
        </div>
    </div>
</div>
