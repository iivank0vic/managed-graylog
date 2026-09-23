<?php
/**
 * Hero "control plane" visual: reference Graylog topology with animated packets
 * and a live-looking (illustrative) log stream. Decorative — hidden from assistive tech,
 * with a text description provided via the figure caption.
 */
declare(strict_types=1);

$sources = [
    ['linux-01', 'beats', 83],
    ['k8s-node', 'otlp', 201],
    ['nginx-edge', 'gelf', 319],
    ['fw-core', 'syslog', 437],
];
$dataNodes = [46, 190, 334];
?>
<figure class="cp" data-control-plane>
  <div class="cp__chrome">
    <span class="cp__dots" aria-hidden="true"><i></i><i></i><i></i></span>
    <span class="cp__path mono">topology / <b>prod-logging</b></span>
    <span class="cp__badge mono">reference architecture</span>
  </div>

  <div class="cp__stage" aria-hidden="true">
    <svg class="cp__svg" viewBox="0 0 520 540" preserveAspectRatio="xMidYMid meet" focusable="false">
      <defs>
        <linearGradient id="cp-line" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0" stop-color="#38bdf8" stop-opacity=".55"/>
          <stop offset="1" stop-color="#818cf8" stop-opacity=".35"/>
        </linearGradient>
        <linearGradient id="cp-box" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0" stop-color="#101723"/>
          <stop offset="1" stop-color="#0b1018"/>
        </linearGradient>
        <radialGradient id="cp-pkt">
          <stop offset="0" stop-color="#e0fbff"/>
          <stop offset=".4" stop-color="#38e1ff"/>
          <stop offset="1" stop-color="#38e1ff" stop-opacity="0"/>
        </radialGradient>
        <radialGradient id="cp-pkt-v">
          <stop offset="0" stop-color="#f1e9ff"/>
          <stop offset=".4" stop-color="#a78bfa"/>
          <stop offset="1" stop-color="#a78bfa" stop-opacity="0"/>
        </radialGradient>
      </defs>

      <!-- Edges -->
      <g class="cp-edges" fill="none" stroke="url(#cp-line)" stroke-width="1.25">
        <?php foreach ($sources as $i => [, , $x]): ?>
          <path id="cp-e-src<?= $i ?>" d="M<?= $x ?> 56 V100"/>
        <?php endforeach; ?>
        <path id="cp-e-lb1" d="M260 152 C260 176 140 170 140 196"/>
        <path id="cp-e-lb2" d="M260 152 C260 176 380 170 380 196"/>
        <path id="cp-e-g1d" d="M140 274 V318"/>
        <path id="cp-e-g2d" d="M380 274 V318"/>
        <path id="cp-e-ds" d="M399 402 V446"/>
        <path class="cp-dashed" id="cp-e-mongo" d="M30 238 H14 V481 H30"/>
        <path class="cp-dashed" id="cp-e-mongo2" d="M490 238 H506 V424 H200 V446"/>
        <path class="cp-dashed" d="M250 494 H270"/>
      </g>

      <!-- Protocol labels -->
      <g class="cp-proto">
        <?php foreach ($sources as [, $proto, $x]): ?>
          <text x="<?= $x + 7 ?>" y="82"><?= e($proto) ?></text>
        <?php endforeach; ?>
      </g>

      <!-- Sources -->
      <g class="cp-sources">
        <text class="cp-label" x="30" y="14">LOG SOURCES</text>
        <?php foreach ($sources as [$name, , $x]): ?>
          <g class="cp-chip">
            <rect x="<?= $x - 53 ?>" y="24" width="106" height="32" rx="6"/>
            <circle cx="<?= $x - 40 ?>" cy="40" r="2.5" class="cp-ok"/>
            <text x="<?= $x - 31 ?>" y="44"><?= e($name) ?></text>
          </g>
        <?php endforeach; ?>
      </g>

      <!-- Ingestion -->
      <g class="cp-box">
        <rect x="30" y="100" width="460" height="52" rx="8"/>
        <text class="cp-label" x="46" y="122">INGESTION LAYER</text>
        <text class="cp-sub" x="46" y="140">load balancer · tls · :1514 syslog · :12201 gelf · :5044 beats · :4317 otlp</text>
      </g>

      <!-- Graylog nodes -->
      <?php foreach ([[30, 'graylog-01', 'a'], [270, 'graylog-02', 'b']] as [$x, $name, $v]): ?>
        <g class="cp-box cp-node">
          <rect x="<?= $x ?>" y="196" width="220" height="78" rx="8"/>
          <text class="cp-title" x="<?= $x + 16 ?>" y="220"><?= e($name) ?></text>
          <circle cx="<?= $x + 202 ?>" cy="216" r="3.5" class="cp-ok cp-pulse"/>
          <text class="cp-sub" x="<?= $x + 16 ?>" y="243">journal</text>
          <rect class="cp-track" x="<?= $x + 78 ?>" y="238" width="124" height="5" rx="2.5"/>
          <rect class="cp-bar cp-bar--<?= $v ?>1" x="<?= $x + 78 ?>" y="238" width="124" height="5" rx="2.5"/>
          <text class="cp-sub" x="<?= $x + 16 ?>" y="261">process</text>
          <rect class="cp-track" x="<?= $x + 78 ?>" y="256" width="124" height="5" rx="2.5"/>
          <rect class="cp-bar cp-bar--<?= $v ?>2" x="<?= $x + 78 ?>" y="256" width="124" height="5" rx="2.5"/>
        </g>
      <?php endforeach; ?>

      <!-- Data / search -->
      <g class="cp-box">
        <rect x="30" y="318" width="460" height="84" rx="8"/>
        <text class="cp-label" x="46" y="340">DATA / SEARCH</text>
        <text class="cp-sub" x="474" y="340" text-anchor="end">graylog data node · opensearch</text>
        <?php foreach ($dataNodes as $i => $x): ?>
          <g class="cp-dn">
            <rect x="<?= $x ?>" y="352" width="130" height="36" rx="5"/>
            <text x="<?= $x + 10 ?>" y="374">data-0<?= $i + 1 ?></text>
            <?php for ($s = 0; $s < 4; $s++): ?>
              <rect class="<?= ($s + $i) % 3 === 0 ? 'cp-shard' : 'cp-replica' ?>" x="<?= $x + 68 + $s * 14 ?>" y="365" width="9" height="9" rx="2"/>
            <?php endfor; ?>
          </g>
        <?php endforeach; ?>
      </g>

      <!-- MongoDB -->
      <g class="cp-box">
        <rect x="30" y="446" width="220" height="80" rx="8"/>
        <text class="cp-label" x="46" y="468">MONGODB</text>
        <text class="cp-sub" x="46" y="486">replica set · rs0</text>
        <circle class="cp-rs cp-rs--p" cx="54" cy="506" r="5"/>
        <circle class="cp-rs" cx="74" cy="506" r="5"/>
        <circle class="cp-rs" cx="94" cy="506" r="5"/>
        <text class="cp-sub" x="110" y="510">primary + 2</text>
      </g>

      <!-- Storage -->
      <g class="cp-box">
        <rect x="270" y="446" width="220" height="80" rx="8"/>
        <text class="cp-label" x="286" y="468">STORAGE / BACKUP</text>
        <text class="cp-sub" x="286" y="486">snapshots → object storage</text>
        <g class="cp-disks">
          <rect x="286" y="498" width="34" height="7" rx="2"/>
          <rect x="286" y="508" width="34" height="7" rx="2"/>
          <rect x="326" y="498" width="34" height="7" rx="2"/>
          <rect x="326" y="508" width="34" height="7" rx="2" class="cp-disk-on"/>
        </g>
        <text class="cp-sub" x="370" y="510">restore-tested</text>
      </g>

      <!-- Packets (hidden when prefers-reduced-motion) -->
      <g class="cp-pkts">
        <?php
        $pk = [
            ['cp-e-src0', '1.1', '0', ''], ['cp-e-src1', '1.3', '-.4', ''], ['cp-e-src2', '0.9', '-.7', ''], ['cp-e-src3', '1.2', '-.2', ''],
            ['cp-e-lb1', '1.4', '-.3', ''], ['cp-e-lb2', '1.4', '-1', ''],
            ['cp-e-g1d', '1.0', '-.5', ''], ['cp-e-g2d', '1.0', '-.1', ''],
            ['cp-e-ds', '2.2', '-.9', '-v'], ['cp-e-mongo', '3.4', '-1.2', '-v'], ['cp-e-mongo2', '3.8', '-2.4', '-v'],
        ];
        foreach ($pk as [$path, $dur, $begin, $variant]): ?>
          <circle r="4" fill="url(#cp-pkt<?= $variant ?>)">
            <animateMotion dur="<?= $dur ?>s" begin="<?= $begin ?>s" repeatCount="indefinite" rotate="auto"><mpath href="#<?= $path ?>"/></animateMotion>
          </circle>
        <?php endforeach; ?>
      </g>
    </svg>
  </div>

  <div class="cp__log" aria-hidden="true">
    <div class="cp__log-head mono"><span>$ tail -f streams/all-messages</span><span class="cp__live">live · sample data</span></div>
    <ol class="cp__lines mono" data-logstream>
      <li><time>10:14:02.118</time><b class="lv lv--info">INFO</b><span class="src">nginx-edge</span><span class="msg">GET /api/v1/orders 200 38ms</span></li>
      <li><time>10:14:02.341</time><b class="lv lv--info">INFO</b><span class="src">k8s-node</span><span class="msg">pod checkout-7d9f ready</span></li>
      <li><time>10:14:02.502</time><b class="lv lv--warn">WARN</b><span class="src">linux-01</span><span class="msg">sshd: failed password for invalid user</span></li>
      <li><time>10:14:02.877</time><b class="lv lv--info">INFO</b><span class="src">fw-core</span><span class="msg">accept tcp 10.0.4.12:51544 → 10.0.1.8:443</span></li>
    </ol>
  </div>
  <figcaption class="visually-hidden">Reference architecture: log sources ship over Beats, OpenTelemetry, GELF and syslog to a load-balanced ingestion layer, two Graylog nodes, a three-node Data Node or OpenSearch search layer, a MongoDB replica set, and snapshot-based storage and backup.</figcaption>
</figure>
