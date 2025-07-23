@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <flux:heading class="text-[82px] text-center font-bold leading-[1] mt-14">{{ $title }}</flux:heading>
    <flux:subheading class="font-bold ">{{ $description }}</flux:subheading>
</div>
