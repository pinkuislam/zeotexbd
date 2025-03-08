<div class="row">
    <div class="col-sm-8">
        <div class="form-group @error('title') has-error @enderror">
            <label class="control-label col-sm-3">Title:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" name="title" value="{{ old('title', isset($data) ? $data->title : '') }}">

                @error('title')
                    <span class="help-block">{{ $message }}</span>
                @enderror
            </div>
        </div>
        
        <div class="form-group @error('link') has-error @enderror">
            <label class="control-label col-sm-3">Link:</label>
            <div class="col-sm-9">
                <input type="url" class="form-control" name="link" value="{{ old('link', isset($data) ? $data->link : '') }}">

                @error('link')
                    <span class="help-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group @error('image') has-error @enderror">
            <label class="control-label col-sm-3 required">Image:</label>
            <div class="col-sm-9">
            <x-sp-components::image-input id="image" name="image" path="{{ isset($data) ? MediaUploader::showUrl('sliders', $data->image) : null }}" />
            </div>
        </div>

        <div class="form-group @error('status') has-error @enderror">
            <label class="control-label col-sm-3 required">Status:</label>
            <div class="col-sm-9">
                <select name="status" class="form-control select2" required>
                    @php ($status = old('status', isset($data) ? $data->status : ''))
                    @foreach(['Active', 'Deactivated'] as $sts)
                        <option value="{{ $sts }}" {{ ($status == $sts) ? 'selected' : '' }}>{{ $sts }}</option>
                    @endforeach
                </select>

                @error('status')
                    <span class="help-block">{{ $message }}</span>
                @enderror
            </div>
        </div>
        

        <div class="form-group text-center">
            <button type="submit" class="btn btn-success btn-flat btn-lg">{{ isset($data) ? 'Update' : 'Create' }}</button>
            <button type="reset" class="btn btn-custom btn-flat btn-lg">Clear</button>
        </div>
    </div>
</div>