@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <a href="{{ route('admin.basic.product-cover.index') . qString() }}" class="btn btn-info" style="margin: 10px;">
                Back
            </a>
            <div class="tab-content">
                <form method="POST" action="{{ route('admin.basic.cover-products.ecommerce.store').qString() }}" id="are_you_sure" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="product_id" value="{{$data->id}}">
                    <div class="row">
                        <div class="col-md-8">        
                            <div class="form-group @error('name') has-error @enderror">
                                <label class="required">Name:</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', isset($data) ? $data->name : '') }}" readonly>
                    
                                @error('name')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group @error('description') has-error @enderror">
                                <label>Description:</label>
                                <textarea class="form-control" id="summernote" name="description">{{ old('description', isset($data->otherinfo) ? $data->otherinfo->description : '') }}</textarea>
                    
                                @error('description')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group @error('is_new_arrival') has-error @enderror">
                                <label class="required">Is New Arrival:</label>
                                <select name="is_new_arrival" class="form-control select2 required">
                                    <option value="">Select One</option>
                                    @php ($is_new_arrival = old('is_new_arrival', isset($data->otherinfo) ? $data->otherinfo->is_new_arrival : ''))
                                    @foreach(['Yes', 'No'] as $item)
                                        <option value="{{ $item }}" {{ ($is_new_arrival == $item) ? 'selected' : '' }}>{{ $item }}</option>
                                    @endforeach
                                </select>
                    
                                @error('is_new_arrival')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                    
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px !important;">
                                                <a class="btn btn-success btn-flat btn-sm" onclick="addRow()"><i class="fa fa-plus"></i></a>
                                            </th>
                                            <th>Barcode</th>
                                            <th>Color</th>
                                            <th>Size</th>
                                            <th style="text-align: right;">Old Price</th>
                                            <th style="text-align: right;">Sale Price</th>
                                        </tr>
                                    </thead>
                    
                                    <tbody id="multiple">
                                        @foreach ($items as $key => $item)
                                        <tr id="row{{ $key }}">
                                            <input type="hidden" name="items[{{ $key }}][row_id]" value="{{ $item->id }}">
                                            <td>
                                                <a class="btn btn-danger btn-flat btn-sm" onclick="removeRow({{ $key }})"><i class="fa fa-minus"></i></a>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="items[{{ $key }}][barcode]" value="{{ $item->barcode }}" />
                                            </td>
                                            <td>
                                                <select name="items[{{ $key }}][color_id]" id="color_id{{ $key }}" class="form-control select2">
                                                    <option value="">Select One</option>
                                                    @foreach($colors as $col)
                                                        <option value="{{ $col->id }}" {{ $col->id == $item->color_id ? 'selected' : '' }}>{{ $col->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="items[{{ $key }}][size_id]" id="size_id{{ $key }}" class="form-control select2">
                                                    <option value="">Select One</option>
                                                    @foreach($sizes as $siz)
                                                        <option value="{{ $siz->id }}" {{ $siz->id == $item->size_id ? 'selected' : '' }}>{{ $siz->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="any" min="1" class="form-control" name="items[{{ $key }}][old_price]" value="{{ $item->old_price }}" />
                                            </td>
                                            <td>
                                                <input type="number" step="any" min="1" class="form-control" name="items[{{ $key }}][sale_price]" value="{{ $item->sale_price }}" />
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group @error('category_id') has-error @enderror">
                                <label class="required">Category:</label>
                                <x-category-dropdown 
                                    :categories="$categories" 
                                    :value="old('category_id', isset($data->otherinfo) ? $data->otherinfo->category_id : '')"
                                    field="category_id"
                                    :required="true"
                                    :lastitem="true"
                                />
                    
                                @error('category_id')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group @error('short_description') has-error @enderror">
                                <label>Short Description:</label>
                                <textarea class="form-control" rows="5" cols="10" name="short_description">{{ old('short_description', isset($data->otherinfo) ? $data->otherinfo->short_description : '') }}</textarea>
                    
                                @error('short_description')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group @error('image') has-error @enderror">
                                <label>Image (600 by 800):</label>
                                <x-sp-components::image-input id="image" name="image" path="{{ isset($data->otherinfo) ? MediaUploader::showUrl('products', $data->otherinfo->image) : null }}" />
                            </div>
                    
                            <div class="form-group @error('video_link') has-error @enderror">
                                <label>Video Link:</label>
                                <input type="text" name="video_link" class="form-control" value="{{ old('video_link', isset($data->otherinfo) ? $data->otherinfo->video_link : '') }}">
                    
                                @error('video_link')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Other Images (640 by 640):</label>
                                <div id="imgBox">
                                    @foreach ($otherImages as $ik => $iv)
                                    <div class="imgBox" id="file_row_{{ $ik }}">
                                        <div class="input-group">
                                            <input type="hidden" name="image_ids[{{ $ik }}]" value="{{ $iv->id }}">
                                            <input type="file" class="form-control" name="images[{{ $ik }}]">
                                            @if ($ik == 0)
                                            <span class="input-group-addon" onclick="addFilesRow({{ $ik }})"><i class="fa fa-plus"></i></span>
                                            @else
                                            <span class="input-group-addon" onclick="removeFilesRowAjax({{ $iv->id }}, {{ $ik }})"><i class="fa fa-minus"></i></span>
                                            @endif
                                        </div>
                                        @if (isset($data))
                                        <small>{{  MediaUploader::showUrl('products/' . $data->id, $iv->image) }} </small>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group @error('seo_box') has-error @enderror">
                                <div>
                                    <x-sp-components::seo-meta-input title_field="meta_title" keywords_field="meta_keywords" description_field="meta_description" :data="$data->otherinfo ?? null"/>
                                </div>
                            </div>
                        </div>
                    
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-success btn-flat btn-lg">{{ isset($data) ? 'Update' : 'Create' }}</button>
                            <button type="reset" class="btn btn-custom btn-flat btn-lg">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
                @push('scripts')
                <script>
                    function addRow() {
                        var key = $("tr[id^='row']").length;
                        var colorOptions = $('#color_id0').html();
                        var sizeOptions = $('#size_id0').html();
                
                        var html = `<tr id="row` + key + `">
                            <input type="hidden" name="items[${key}][row_id]">
                            <td>
                                <a class="btn btn-danger btn-flat btn-sm" onclick="removeRow(${key})"><i class="fa fa-minus"></i></a>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="items[${key}][barcode]" />
                            </td>
                            <td>
                                <select name="items[${key}][color_id]" id="color_id${key}" class="form-control select2">
                                    <option value="">Select One</option>
                                    ${colorOptions}
                                </select>
                            </td>
                            <td>
                                <select name="items[${key}][size_id]" id="size_id${key}" class="form-control select2">
                                    <option value="">Select One</option>
                                    ${sizeOptions}
                                </select>
                            </td>
                            <td>
                                <input type="number" step="any" min="1" class="form-control" name="items[${key}][old_price]" />
                            </td>
                            <td>
                                <input type="number" step="any" min="1" class="form-control" name="items[${key}][sale_price]" />
                            </td>
                        </tr>`;
                
                        $('#multiple').append(html);
                        $(`#color_id${key}`).val('');
                        $(`#size_id${key}`).val('');
                
                        select2Init();
                    }
                
                    function removeRow(key) {
                        $(`#row${key}`).remove();
                    }
                
                    function addFilesRow() {
                        var k = $('#imgBox .imgBox').length;
                
                        var html = `<div class="imgBox" id="file_row_${k}">
                            <div class="input-group">
                                <input type="hidden" name="image_ids[${k}]" value="0">
                                <input type="file" class="form-control" name="images[${k}]">
                                <span class="input-group-addon" onclick="removeFilesRow(${k})"><i class="fa fa-minus"></i></span>
                            </div>
                        </div>`;
                        $('#imgBox').append(html);
                    }
                
                    function removeFilesRow(k) {
                        $('#file_row_' + k).remove();
                    }
                
                    function removeFilesRowAjax(id, k) {
                        $.ajax({
                            type: 'POST',
                            dataType: "JSON",
                            data: {id: id},
                            url: "{{ route('admin.basic.cover-products.image-destroy') }}",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                if (res.success) {
                                    $('#file_row_' + k).remove();
                                }
                                alert(res.message);
                            },
                            error: function (err) {
                                alert(err.responseJSON.message);
                            }
                        });
                    }
                    $('#summernote').summernote({
                        height: 200
                    });
            </script>
            @endpush
        </div>
    </div>
</section>
@endsection

