<select name="{{ $field }}" class="form-control select2 {{ $required ? ' required' : '' }}">
    <option value="">Select One</option>
    @if ($lastitem)
        @foreach($categories as $item)
            @if (config('settings.category_layer') == 1)
                <option value="{{ $item->id }}" {{ ($value == $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
            @else
                <optgroup label="{{ $item->name }}">
                    @if (config('settings.category_layer') == 3)
                        @foreach ($item->childs as $child)
                            <optgroup label=" -- {{ $child->name }}">
                                @foreach ($child->childs as $nest)
                                    <option value="{{ $nest->id }}" {{ ($value == $nest->id) ? 'selected' : '' }}> -- {{ $nest->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    @else
                        @foreach ($item->childs as $child)
                            <option value="{{ $child->id }}" {{ ($value == $child->id) ? 'selected' : '' }}> -- {{ $child->name }}</option>
                        @endforeach
                    @endif
                </optgroup>
            @endif
        @endforeach
    @else
        @foreach($categories as $item)
            <option value="{{ $item->id }}" {{ ($value == $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
            @if ($item->relationLoaded('childs'))
                @foreach ($item->childs as $child)
                    <option value="{{ $child->id }}" {{ ($value == $child->id) ? 'selected' : '' }}> -- {{ $child->name }}</option>
                @endforeach
            @endif
        @endforeach
    @endif
</select>