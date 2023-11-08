<table>
  <tr>
      <th>Name</th>
      <th>Date</th>
      <th>Checkin</th>
      <th>Checkout</th>


  </tr>

  <tbody>

      @foreach($data as $item)


      <tr>
          <td>{{ $item->name }}</td>
          <td>{{ $item->date }}</td>
          <td>{{ $item->in_time }}</td>
          <td>{{ $item->out_time }}</td>




      </tr>
      @endforeach

  </tbody>
</table>
