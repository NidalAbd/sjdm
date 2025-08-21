@foreach($uniqueCategories as $category)
    <option value="{{ $category }}">{{ ucfirst($category) }}</option>
@endforeach
