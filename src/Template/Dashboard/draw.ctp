<div class="row" id="reporter">
<!-- user-business start -->
<div class="ui segment row">
    <div class="ui header segment">业务员订单数据</div>

    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h4>业务员本月成交排名 <small></small></h4>
          <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
        <table class="" style="width:100%">
          <tr>
            <th style="width:37%;">
              <p>Top</p>
            </th>
            <th>
              <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
                <p class="">业务员</p>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
                <p class="">单数</p>
              </div>
            </th>
          </tr>
          <tr>
            <td>
              <canvas id="userBusinessSignedMonthCanvas" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
            </td>
            <td>
              <?php 
                foreach ($userBusinessSignedMonth as $user){ ?>
              <div class="clearfix">
                <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
                  <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'view', $user['userid']]) ?>">
                  <?= $user['username']?>
                  </a>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
                  <?= $user['total_signed']?>
                </div>
              </div>
              <?php 
                } ?>
            </td>
          </tr>
        </table>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h4>业务员本月新增订单数 <small></small></h4>
          <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
        <table class="" style="width:100%">
          <tr>
            <th style="width:37%;">
              <p>Top</p>
            </th>
            <th>
              <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
                <p class="">业务员</p>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
                <p class="">单数</p>
              </div>
            </th>
          </tr>
          <tr>
            <td>
              <canvas id="userBusinessNewMonthCanvas" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
            </td>
            <td>
              <?php 
                foreach ($userBusinessNewMonth as $user){ ?>
              <div class="clearfix">
                <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
                  <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'view', $user['userid']]) ?>">
                  <?= $user['username']?>
                  </a>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
                  <?= $user['total_signed']?>
                </div>
              </div>
              <?php 
                } ?>
            </td>
          </tr>
        </table>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h4>业务员进行中订单数 <small></small></h4>
          <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
        <table class="" style="width:100%">
          <tr>
            <th style="width:37%;">
              <p>Top</p>
            </th>
            <th>
              <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
                <p class="">业务员</p>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
                <p class="">单数</p>
              </div>
            </th>
          </tr>
          <tr>
            <td>
              <canvas id="userBusinessIngCanvas" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
            </td>
            <td>
              <?php 
                foreach ($userBusinessIng as $user){ ?>
              <div class="clearfix">
                <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
                  <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'view', $user['userid']]) ?>">
                  <?= $user['username']?>
                  </a>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
                  <?= $user['total_signed']?>
                </div>
              </div>
              <?php 
                } ?>
            </td>
          </tr>
        </table>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h4>业务员历史成交订单数 <small></small></h4>
          <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
        <table class="" style="width:100%">
          <tr>
            <th style="width:37%;">
              <p>Top</p>
            </th>
            <th>
              <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
                <p class="">业务员</p>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
                <p class="">单数</p>
              </div>
            </th>
          </tr>
          <tr>
            <td>
              <canvas id="userBusinessSignedHistoryCanvas" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
            </td>
            <td>
              <?php 
                foreach ($userBusinessSignedHistory as $user){ ?>
              <div class="clearfix">
                <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
                  <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'view', $user['userid']]) ?>">
                  <?= $user['username']?>
                  </a>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
                  <?= $user['total_signed']?>
                </div>
              </div>
              <?php 
                } ?>
            </td>
          </tr>
        </table>
        </div>
      </div>
    </div>
</div>
<!-- user-business end -->

<!-- user-customer start -->
<div class="ui segment row">
  <div class="ui header segment">业务员客户数据</div>

  <div class="col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h4>业务员本月新增客户人数 <small></small></h4>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
      <table class="" style="width:100%">
        <tr>
          <th style="width:37%;">
            <p>Top</p>
          </th>
          <th>
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
              <p class="">业务员</p>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
              <p class="">人数</p>
            </div>
          </th>
        </tr>
        <tr>
          <td>
            <canvas id="userCustomerNewMonthCanvas" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
          </td>
          <td>
            <?php 
              foreach ($userCustomerNewMonth as $user){ ?>
            <div class="clearfix">
              <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
                <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'view', $user['userid']]) ?>">
                <?= $user['username']?>
                </a>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
                <?= $user['total_signed']?>
              </div>
            </div>
            <?php 
              } ?>
          </td>
        </tr>
      </table>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h4>业务员客户人数 <small></small></h4>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
      <table class="" style="width:100%">
        <tr>
          <th style="width:37%;">
            <p>Top</p>
          </th>
          <th>
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
              <p class="">业务员</p>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
              <p class="">人数</p>
            </div>
          </th>
        </tr>
        <tr>
          <td>
            <canvas id="userCustomerNewHistoryCanvas" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
          </td>
          <td>
            <?php 
              foreach ($userCustomerNewHistory as $user){ ?>
            <div class="clearfix">
              <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
                <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'view', $user['userid']]) ?>">
                <?= $user['username']?>
                </a>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
                <?= $user['total_signed']?>
              </div>
            </div>
            <?php 
              } ?>
          </td>
        </tr>
      </table>
      </div>
    </div>
  </div>
</div>
<!-- user-customer end -->

<!-- customer-business start -->
<div class="ui segment row">
  <div class="ui header segment">客户订单数据</div>

  <div class="col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h4>客户本月新增活动次数 <small></small></h4>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
      <table class="" style="width:100%">
        <tr>
          <th style="width:37%;">
            <p>Top</p>
          </th>
          <th>
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
              <p class="">客户</p>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
              <p class="">活动次数</p>
            </div>
          </th>
        </tr>
        <tr>
          <td>
            <canvas id="customerPartedTop10MonthCanvas" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
          </td>
          <td>
            <?php 
              foreach ($customerPartedTop10Month as $customer){ ?>
            <div class="clearfix">
              <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
                <a href="<?= $this->Url->build(['controller' => 'Customers', 'action' => 'view', $customer['customerid']]) ?>">
                <?= $customer['name'].'(+'.$codes[$customer['customer']['customer_mobiles'][0]['country_code_id']].'-'.$customer['customer']['customer_mobiles'][0]['mobile'].')'?>
                </a>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
                <?= $customer['total']?>
              </div>
            </div>
            <?php 
              } ?>
          </td>
        </tr>
      </table>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h4>客户本月成交次数 <small></small></h4>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
      <table class="" style="width:100%">
        <tr>
          <th style="width:37%;">
            <p>Top</p>
          </th>
          <th>
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
              <p class="">客户</p>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
              <p class="">成交数</p>
            </div>
          </th>
        </tr>
        <tr>
          <td>
            <canvas id="customerSignedTop10MonthCanvas" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
          </td>
          <td>
            <?php 
              foreach ($customerSignedTop10Month as $customer){ ?>
            <div class="clearfix">
              <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
                <a href="<?= $this->Url->build(['controller' => 'Customers', 'action' => 'view', $customer['customerid']]) ?>">
                <?= $customer['name'].'(+'.$codes[$customer['customer']['customer_mobiles'][0]['country_code_id']].'-'.$customer['customer']['customer_mobiles'][0]['mobile'].')'?>
                </a>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
                <?= $customer['total']?>
              </div>
            </div>
            <?php 
              } ?>
          </td>
        </tr>
      </table>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h4>客户参与中活动次数 <small></small></h4>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
      <table class="" style="width:100%">
        <tr>
          <th style="width:37%;">
            <p>Top</p>
          </th>
          <th>
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
              <p class="">客户</p>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
              <p class="">活动次数</p>
            </div>
          </th>
        </tr>
        <tr>
          <td>
            <canvas id="customerIngTop10Canvas" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
          </td>
          <td>
            <?php 
              foreach ($customerIngTop10 as $customer){ ?>
            <div class="clearfix">
              <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
                <a href="<?= $this->Url->build(['controller' => 'Customers', 'action' => 'view', $customer['customerid']]) ?>">
                <?= $customer['name'].'(+'.$codes[$customer['customer']['customer_mobiles'][0]['country_code_id']].'-'.$customer['customer']['customer_mobiles'][0]['mobile'].')'?>
                </a>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3">
                <?= $customer['total']?>
              </div>
            </div>
            <?php 
              } ?>
          </td>
        </tr>
      </table>
      </div>
    </div>
  </div>
</div>
<!-- customer-business end -->

<!-- half-year data start -->
<div class="ui segment row">
  <div class="ui header">半年内客户及订单变化数据</div>
  <div class="col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h4>客户变化 <small></small></h4>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <canvas id="newCustomerByMonthCanvas"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h4>订单变化 <small></small></h4>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <canvas id="newBusinessByMonthCanvas"></canvas>
      </div>
    </div>
  </div>
</div>
<!-- half-year data end -->

<?= $this->start('script') ?>
<?= $this->Html->script('vendors/Chart.min.js') ?>
 <!-- Doughnut Chart -->
    <script>
      $(document).ready(function() {
        var options = {
          legend: false,
          responsive: false
        };
        new Chart(document.getElementById("userBusinessSignedMonthCanvas"), {
          type: 'doughnut',
          tooltipFillColor: "rgba(51, 51, 51, 0.55)",
          data: {
            labels: [
            <?php foreach ($userBusinessSignedMonth as $user){ ?>
              <?php echo '"'.$user['username'].'",'; ?>
            <?php } ?>
            ],
            datasets: [{
              data: [
              <?php foreach ($userBusinessSignedMonth as $user){ ?>
                <?php echo $user['total_signed'].','; ?>
              <?php } ?>
              ],
              backgroundColor: [
                "#D462FF",
                "#C12869",
                "#E42217",
                "#FF8040",
                "#FBB917",
                "#6AFB92",
                "#50EBEC",
                "#82CAFA",
                "#0041C2",
                "#736F6E"
              ],
              hoverBackgroundColor: [
                "#E238EC",
                "#C12267",
                "#F62817",
                "#F88017",
                "#FBB117",
                "#98FF98",
                "#4EE2EC",
                "#82CAFF",
                "#0020C2",
                "#837E7C"
              ]
            }]
          },
          options: options
        });
        new Chart(document.getElementById("userBusinessNewMonthCanvas"), {
          type: 'doughnut',
          tooltipFillColor: "rgba(51, 51, 51, 0.55)",
          data: {
            labels: [
            <?php foreach ($userBusinessNewMonth as $user){ ?>
              <?php echo '"'.$user['username'].'",'; ?>
            <?php } ?>
            ],
            datasets: [{
              data: [
              <?php foreach ($userBusinessNewMonth as $user){ ?>
                <?php echo $user['total_signed'].','; ?>
              <?php } ?>
              ],
              backgroundColor: [
                "#D462FF",
                "#C12869",
                "#E42217",
                "#FF8040",
                "#FBB917",
                "#6AFB92",
                "#50EBEC",
                "#82CAFA",
                "#0041C2",
                "#736F6E"
              ],
              hoverBackgroundColor: [
                "#E238EC",
                "#C12267",
                "#F62817",
                "#F88017",
                "#FBB117",
                "#98FF98",
                "#4EE2EC",
                "#82CAFF",
                "#0020C2",
                "#837E7C"
              ]
            }]
          },
          options: options
        });
        new Chart(document.getElementById("userBusinessIngCanvas"), {
          type: 'doughnut',
          tooltipFillColor: "rgba(51, 51, 51, 0.55)",
          data: {
            labels: [
            <?php foreach ($userBusinessIng as $user){ ?>
              <?php echo '"'.$user['username'].'",'; ?>
            <?php } ?>
            ],
            datasets: [{
              data: [
              <?php foreach ($userBusinessIng as $user){ ?>
                <?php echo $user['total_signed'].','; ?>
              <?php } ?>
              ],
              backgroundColor: [
                "#D462FF",
                "#C12869",
                "#E42217",
                "#FF8040",
                "#FBB917",
                "#6AFB92",
                "#50EBEC",
                "#82CAFA",
                "#0041C2",
                "#736F6E"
              ],
              hoverBackgroundColor: [
                "#E238EC",
                "#C12267",
                "#F62817",
                "#F88017",
                "#FBB117",
                "#98FF98",
                "#4EE2EC",
                "#82CAFF",
                "#0020C2",
                "#837E7C"
              ]
            }]
          },
          options: options
        });
        new Chart(document.getElementById("userBusinessSignedHistoryCanvas"), {
          type: 'doughnut',
          tooltipFillColor: "rgba(51, 51, 51, 0.55)",
          data: {
            labels: [
            <?php foreach ($userBusinessSignedHistory as $user){ ?>
              <?php echo '"'.$user['username'].'",'; ?>
            <?php } ?>
            ],
            datasets: [{
              data: [
              <?php foreach ($userBusinessSignedHistory as $user){ ?>
                <?php echo $user['total_signed'].','; ?>
              <?php } ?>
              ],
              backgroundColor: [
                "#D462FF",
                "#C12869",
                "#E42217",
                "#FF8040",
                "#FBB917",
                "#6AFB92",
                "#50EBEC",
                "#82CAFA",
                "#0041C2",
                "#736F6E"
              ],
              hoverBackgroundColor: [
                "#E238EC",
                "#C12267",
                "#F62817",
                "#F88017",
                "#FBB117",
                "#98FF98",
                "#4EE2EC",
                "#82CAFF",
                "#0020C2",
                "#837E7C"
              ]
            }]
          },
          options: options
        });
        new Chart(document.getElementById("userCustomerNewMonthCanvas"), {
          type: 'doughnut',
          tooltipFillColor: "rgba(51, 51, 51, 0.55)",
          data: {
            labels: [
            <?php foreach ($userCustomerNewMonth as $user){ ?>
              <?php echo '"'.$user['username'].'",'; ?>
            <?php } ?>
            ],
            datasets: [{
              data: [
              <?php foreach ($userCustomerNewMonth as $user){ ?>
                <?php echo $user['total_signed'].','; ?>
              <?php } ?>
              ],
              backgroundColor: [
                "#D462FF",
                "#C12869",
                "#E42217",
                "#FF8040",
                "#FBB917",
                "#6AFB92",
                "#50EBEC",
                "#82CAFA",
                "#0041C2",
                "#736F6E"
              ],
              hoverBackgroundColor: [
                "#E238EC",
                "#C12267",
                "#F62817",
                "#F88017",
                "#FBB117",
                "#98FF98",
                "#4EE2EC",
                "#82CAFF",
                "#0020C2",
                "#837E7C"
              ]
            }]
          },
          options: options
        });
        new Chart(document.getElementById("userCustomerNewHistoryCanvas"), {
          type: 'doughnut',
          tooltipFillColor: "rgba(51, 51, 51, 0.55)",
          data: {
            labels: [
            <?php foreach ($userCustomerNewHistory as $user){ ?>
              <?php echo '"'.$user['username'].'",'; ?>
            <?php } ?>
            ],
            datasets: [{
              data: [
              <?php foreach ($userCustomerNewHistory as $user){ ?>
                <?php echo $user['total_signed'].','; ?>
              <?php } ?>
              ],
              backgroundColor: [
                "#D462FF",
                "#C12869",
                "#E42217",
                "#FF8040",
                "#FBB917",
                "#6AFB92",
                "#50EBEC",
                "#82CAFA",
                "#0041C2",
                "#736F6E"
              ],
              hoverBackgroundColor: [
                "#E238EC",
                "#C12267",
                "#F62817",
                "#F88017",
                "#FBB117",
                "#98FF98",
                "#4EE2EC",
                "#82CAFF",
                "#0020C2",
                "#837E7C"
              ]
            }]
          },
          options: options
        });
        new Chart(document.getElementById("customerPartedTop10MonthCanvas"), {
          type: 'doughnut',
          tooltipFillColor: "rgba(51, 51, 51, 0.55)",
          data: {
            labels: [
            <?php foreach ($customerPartedTop10Month as $customer){ ?>
              <?php echo '"'.$customer['name'].'",'; ?>
            <?php } ?>
            ],
            datasets: [{
              data: [
              <?php foreach ($customerPartedTop10Month as $customer){ ?>
                <?php echo $customer['total'].','; ?>
              <?php } ?>
              ],
              backgroundColor: [
                "#D462FF",
                "#C12869",
                "#E42217",
                "#FF8040",
                "#FBB917",
                "#6AFB92",
                "#50EBEC",
                "#82CAFA",
                "#0041C2",
                "#736F6E"
              ],
              hoverBackgroundColor: [
                "#E238EC",
                "#C12267",
                "#F62817",
                "#F88017",
                "#FBB117",
                "#98FF98",
                "#4EE2EC",
                "#82CAFF",
                "#0020C2",
                "#837E7C"
              ]
            }]
          },
          options: options
        });
        new Chart(document.getElementById("customerIngTop10Canvas"), {
          type: 'doughnut',
          tooltipFillColor: "rgba(51, 51, 51, 0.55)",
          data: {
            labels: [
            <?php foreach ($customerIngTop10 as $customer){ ?>
              <?php echo '"'.$customer['name'].'",'; ?>
            <?php } ?>
            ],
            datasets: [{
              data: [
              <?php foreach ($customerIngTop10 as $customer){ ?>
                <?php echo $customer['total'].','; ?>
              <?php } ?>
              ],
              backgroundColor: [
                "#D462FF",
                "#C12869",
                "#E42217",
                "#FF8040",
                "#FBB917",
                "#6AFB92",
                "#50EBEC",
                "#82CAFA",
                "#0041C2",
                "#736F6E"
              ],
              hoverBackgroundColor: [
                "#E238EC",
                "#C12267",
                "#F62817",
                "#F88017",
                "#FBB117",
                "#98FF98",
                "#4EE2EC",
                "#82CAFF",
                "#0020C2",
                "#837E7C"
              ]
            }]
          },
          options: options
        });
        new Chart(document.getElementById("customerSignedTop10MonthCanvas"), {
          type: 'doughnut',
          tooltipFillColor: "rgba(51, 51, 51, 0.55)",
          data: {
            labels: [
            <?php foreach ($customerSignedTop10Month as $customer){ ?>
              <?php echo '"'.$customer['name'].'",'; ?>
            <?php } ?>
            ],
            datasets: [{
              data: [
              <?php foreach ($customerSignedTop10Month as $customer){ ?>
                <?php echo $customer['total'].','; ?>
              <?php } ?>
              ],
              backgroundColor: [
                "#D462FF",
                "#C12869",
                "#E42217",
                "#FF8040",
                "#FBB917",
                "#6AFB92",
                "#50EBEC",
                "#82CAFA",
                "#0041C2",
                "#736F6E"
              ],
              hoverBackgroundColor: [
                "#E238EC",
                "#C12267",
                "#F62817",
                "#F88017",
                "#FBB117",
                "#98FF98",
                "#4EE2EC",
                "#82CAFF",
                "#0020C2",
                "#837E7C"
              ]
            }]
          },
          options: options
        });

        new Chart(document.getElementById("newCustomerByMonthCanvas"), {
            type: 'line',
            data: {
                labels: [
                            <?php foreach ($month_lately_arr as $value): ?>
                                <?= $value.','?>
                            <?php endforeach ?>
                        ],
                datasets:[
                    {
                        label: '<?= $labelArr['total'][0]?>',
                        backgroundColor: '<?= $labelArr['total'][1]?>',
                        borderColor: '<?= $labelArr['total'][1]?>',
                        data: [
                            <?php foreach ($month_lately_arr as $month): ?>
                                <?= (isset($newCustomerByMonth[$month]) ? $newCustomerByMonth[$month]['total']:0).','?>
                            <?php endforeach ?>
                        ],
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text:'客户数据'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: '月份'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: '人数'
                        }
                    }]
                }
            }
        });

        new Chart(document.getElementById("newBusinessByMonthCanvas"), {
            type: 'line',
            data: {
                labels: [
                            <?php foreach ($month_lately_arr as $value): ?>
                                <?= $value.','?>
                            <?php endforeach ?>
                        ],
                datasets:[
                    <?php foreach ($labelArr as $key =>  $value): ?>
                    {
                        label: '<?= $value[0]?>',
                        backgroundColor: '<?= $value[1]?>',
                        borderColor: '<?= $value[1]?>',
                        data: [
                            <?php foreach ($month_lately_arr as $month): ?>
                                <?= (isset($newBusinessByMonth[$month][$key]) ? $newBusinessByMonth[$month][$key] :0).','?>
                            <?php endforeach ?>
                        ],
                        fill: false,
                    }, 
                    <?php endforeach ?>
                ]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text:'订单数据'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: '时间'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: '人数'
                        }
                    }]
                }
            }
        });

      });
    </script>
    <!-- /Doughnut Chart -->
<?= $this->end() ?>