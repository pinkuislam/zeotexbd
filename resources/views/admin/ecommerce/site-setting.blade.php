@extends('layouts.app')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="javascript:void(0);">
                    <i class="fa fa-cogs" aria-hidden="true"></i> Site Setting
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ route('admin.ecommerce.settings.store') }}" id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group @error('email') has-error @enderror">
                                    <label class="control-label col-sm-3">Email:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="email" value="{{ old('email', isset($data) ? $data->email : '') }}">
                        
                                        @error('email')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-group @error('phone') has-error @enderror">
                                    <label class="control-label col-sm-3">Phone:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="phone" value="{{ old('phone', isset($data) ? $data->phone : '') }}">
                        
                                        @error('phone')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                        
                                <div class="form-group @error('address') has-error @enderror">
                                    <label class="control-label col-sm-3">Address:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="address" value="{{ old('address', isset($data) ? $data->address : '') }}">
                        
                                        @error('address')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group @error('image') has-error @enderror">
                                    <label class="control-label col-sm-3">Logo: <span>(1020 * 310)</span></label>
                                    <div class="col-sm-9">
                                    <x-sp-components::image-input id="logo" name="logo" path="{{ isset($data) ? MediaUploader::showUrl('sitesetting', $data->logo) : null }}" />
                                    </div>
                                </div>  
                                <div class="form-group @error('favicon') has-error @enderror">
                                    <label class="control-label col-sm-3">Favicon: <span>(80 * 80)</span></label>
                                    <div class="col-sm-9">
                                    <x-sp-components::image-input id="favicon" name="favicon" path="{{ isset($data) ? MediaUploader::showUrl('sitesetting', $data->favicon) : null }}" />
                                    </div>
                                </div>  
                                <div class="form-group">
                                    <label class="control-label col-sm-3 required">Map Iframe:</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" name="map_iframe" rows="6">{{ old('map_iframe', isset($data) ? $data->map_iframe : '') }}</textarea>
                                    </div>
                                </div>
                        
                                <div class="form-group @error('facebook') has-error @enderror">
                                    <label class="control-label col-sm-3">Facebook:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="facebook" value="{{ old('facebook', isset($data) ? $data->facebook : '') }}">
                        
                                        @error('facebook')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group @error('twitter') has-error @enderror">
                                    <label class="control-label col-sm-3">Twitter:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="twitter" value="{{ old('twitter', isset($data) ? $data->twitter : '') }}">
                        
                                        @error('twitter')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group @error('linkedin') has-error @enderror">
                                    <label class="control-label col-sm-3">LinkedIn:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="linkedin" value="{{ old('linkedin', isset($data) ? $data->linkedin : '') }}">
                        
                                        @error('linkedin')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group @error('instagram') has-error @enderror">
                                    <label class="control-label col-sm-3">Instagram:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="instagram" value="{{ old('instagram', isset($data) ? $data->instagram : '') }}">
                        
                                        @error('instagram')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group @error('youtube') has-error @enderror">
                                    <label class="control-label col-sm-3">Youtube:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="youtube" value="{{ old('youtube', isset($data) ? $data->youtube : '') }}">
                        
                                        @error('youtube')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group @error('google') has-error @enderror">
                                    <label class="control-label col-sm-3">Google:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="google" value="{{ old('google', isset($data) ? $data->google : '') }}">
                        
                                        @error('google')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group @error('organization_name') has-error @enderror">
                                    <label class="control-label col-sm-3">Organization Name:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="organization_name" value="{{ old('organization_name', isset($data) ? $data->organization_name : '') }}">
                        
                                        @error('organization_name')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group @error('moto') has-error @enderror">
                                    <label class="control-label col-sm-3">Moto:</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" name="moto" rows="6">{{ old('moto', isset($data) ? $data->moto : '') }}</textarea>
                                        @error('moto')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group @error('seo_box') has-error @enderror">
                                    <div class="col-sm-9 col-sm-offset-3">
                                        <x-sp-components::seo-meta-input title_field="meta_title" keywords_field="meta_keywords" description_field="meta_description" :data="$data ?? null"/>
                                    </div>
                                </div>
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-success btn-flat btn-lg">{{ isset($data) ? 'Update' : 'Create' }}</button>
                                    <button type="reset" class="btn btn-custom btn-flat btn-lg">Clear</button>
                                </div>
                            </div>
                        </div>                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
