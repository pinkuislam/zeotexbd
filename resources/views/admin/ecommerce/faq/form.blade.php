<div class="row">
    <div class="col-sm-8">
        <div class="form-group @error('question') has-error @enderror">
            <label class="control-label col-sm-3 required">Question:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" name="question" value="{{ old('question', isset($data) ? $data->question : '') }}">

                @error('question')
                    <span class="help-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3 required">Answer:</label>
            <div class="col-sm-9">
                <textarea class="form-control" id="summernote" name="answer" rows="6">{{ old('answer', isset($data) ? $data->answer : '') }}</textarea>
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
            <button type="submit" class="btn btn-success btn-flat btn-lg">{{ isset($data) ? 'Update' : 'Create' }}</button>
            <button type="reset" class="btn btn-custom btn-flat btn-lg">Clear</button>
        </div>
    </div>
</div>
