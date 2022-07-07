@extends('layouts.layout')
@section('title',  'Hashrate by date')
@section('page-body')
    <form method="POST" action="{{route("show")}}">
        <div class="row">
            <div class="col-sm">
                <label for="exampleInputEmail1" class="form-label">Тариф</label>
                <input name="tariff" type="text" class="form-control" value="{{request('tariff')}}">
            </div>
            <div class="col-sm">
                <label for="exampleInputPassword1" class="form-label">Потребление</label>
                <input name="consumption" type="text" class="form-control" id="exampleInputPassword1" value="{{request('consumption')}}">
            </div>
            <div class="col-sm">
                <label for="exampleInputPassword1" class="form-label">Дата начала расчета</label>
                <input name="date_start" type="text" class="form-control" id="exampleInputPassword1" value="{{request('date_start')}}">
            </div>
            <div class="col-sm">
                <label for="exampleInputPassword1" class="form-label">Дата окончания расчета</label>
                <input name="date_end" type="text" class="form-control" id="exampleInputPassword1" value="{{request('date_end')}}">
            </div>
        </div>
        @csrf
        <button type="submit" class="btn btn-primary">Расчет</button>
    </form>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($result)
    @foreach($result['rows'] as $date=>$row)
        <table class="table table-sm">
            <thead>
            <tr>
                <th class="table-primary" scope="col">Воркер</th>
                @foreach($row['data_by_date'] as $k=>$data)
                    <th class="table-primary" scope="col">{{$k}}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            <tr>
                Сумма за день: {{$row['total']}}
            </tr>
            <tr>
                <td scope="row">{{$date}}</td>
                @foreach($row['data_by_date'] as $data)
                    <td>
                        <span>{{$data['hashrate']}}</span>
                        <span>|</span>
                        <span>{{$data['day_sum']}} руб.</span>
                    </td>
                @endforeach
            </tr>
            </tbody>
        </table>
    @endforeach
    <p>Итоговая сумма {{$result['total']}}</p>
    @else
        <h2>За данный период нет данных</h2>
    @endif
@endsection
