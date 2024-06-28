<div style="background: #eee; border-radius: 10px;">
    <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td>
                <div style="background:#fff; border-radius: 10px;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr>
                            <td valign="middle" style="padding-left:30px;background-color:#3a5795;color:#fff;padding:20px 40px;font-size: 21px;border-top-left-radius: 10px;border-top-right-radius: 10px;">{{$name}}</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr style="padding:40px 40px 0 40px;display:table-cell">
                            <td style="font-size:24px;line-height:1.5;color:#000;margin-top:40px">Mã xác minh email</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;color:#333;padding:24px 40px 0 40px">
                                Kính gửi quý khách,
                                <br />
                                <br />
                                Mã xác minh của bạn là: {{$code}}, <br />
                                Vui lòng xác minh trong vòng 5 phút. Nếu bạn không yêu cầu mã này, vui lòng bỏ qua.
                            </td>
                        </tr>
                        <tr style="padding:40px;display:table-cell">
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div style="border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>
                            <td style="padding:20px 40px;font-size:12px;color:#999;line-height:20px;background:#f7f7f7"><a href="{{$url}}" style="font-size:14px;color:#929292">Quay lại {{$name}}</a></td>
                        </tr>
                        </tbody>
                    </table>
                </div></td>
        </tr>
        </tbody>
    </table>
</div>