<x-mail::message>
Hello

<x-mail::panel>
@if ($admin)
  A member has claimed/trade a reward.<br/>
  Member Id: {{$member_id}}<br/>
  Member Name: {{$member_name}}<br/>
  Reward Id: {{$reward_id}}<br/>
  Reward: {{$reward_name}}<br/>
@else
  You have successfully claimed/trade the reward. <br/>
  You will be contacted by the admin please wait.<br/>
  Reward Id: {{$reward_id}}<br/>
  Reward: {{$reward_name}}<br/>
@endif


</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
