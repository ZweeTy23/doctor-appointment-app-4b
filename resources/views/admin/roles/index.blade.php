<x-admin-layout
    title="Roles | Barkomedic"
    :breadcrumbs="[
    [
    'name'=>'Dashboard',
    'route'=> route('admin.dashboard'),
],
['name' =>'Roles',
],


]">
@livewire('admin.data-tables.role-table')

</x-admin-layout>
