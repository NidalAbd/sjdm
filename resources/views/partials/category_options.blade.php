<option value="all">Select Category</option>
@foreach($uniqueCategories as $category)
    <option value="{{ $category }}">{{ ucfirst($category) }}</option>
@endforeach
