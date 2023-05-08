const gulp = require('gulp');
const autoprefixer = require('gulp-autoprefixer');
const babel = require('gulp-babel');
const concat = require('gulp-concat');
const iconfont = require('gulp-iconfont');
const iconfontCss = require('gulp-iconfont-css');
const minifyCSS = require('gulp-clean-css');
const plumber = require('gulp-plumber');
const rename = require('gulp-rename');
const sass = require('gulp-sass')(require('sass'));
const sourcemaps = require('gulp-sourcemaps');
const uglify = require('gulp-uglify-es').default;
const zip = require('gulp-zip');
const wpPot = require('./tools/vendor/wp-pot');

gulp.task('watch', () => gulp.watch('src/**/*.scss', gulp.series('styles')));

gulp.task(
    'styles-build',
    () => gulp
        .src(
            [
                './src/admin/scss/app.scss',
                './src/front/scss/app.scss',
                './src/front/scss/app-full.scss'
            ],
            {base: '.'}
        )
        .pipe(plumber({
            errorHandler: (err) => {
                console.log(err);
                this.emit('end');
            }
        }))
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(sass({errLogToConsole: true}))
        .pipe(sourcemaps.write())
        .pipe(autoprefixer({
            overrideBrowserslist: [
                'last 2 version',
                'not dead',
                'not ie <= 11',
                'iOS >= 12'
            ],
            cascade: true
        }))
        .pipe(gulp.dest('.'))
);

gulp.task(
    'styles-copy',
    () => gulp
        .src(
            [
                './src/admin/scss/*',
                './src/front/scss/*',
                '!./**/*.scss'
            ],
            {
                base: '.',
                nodir: true
            }
        )
        .pipe(rename((path) => {
            path.dirname = path.dirname.replace('src', 'assets');
            path.dirname = path.dirname.replace('scss', 'css');
        }))
        .pipe(gulp.dest('.'))
);

gulp.task(
    'styles-compress',
    () => gulp
        .src(
            [
                './assets/**/css/*.css',
                '!./assets/**/css/**/*.min.css'
            ],
            {base: '.'}
        )
        .pipe(minifyCSS({
            compatibility: 'ie8',
            level: {1: {specialComments: 0}}
        }))
        .pipe(rename((path) => {
            path.extname = '.min' + path.extname;
        }))
        .pipe(gulp.dest('.'))
);

gulp.task(
    'styles',
    gulp.series(
        'styles-build',
        'styles-copy',
        'styles-compress'
    )
);

gulp.task(
    'icon-font',
    () => gulp
        .src('./src/front/icons/*.svg')
        .pipe(iconfontCss({
            fontName: 'woocommerce-products-wizard',
            fontPath: '../fonts/icons',
            path: './src/front/icons/_template',
            targetPath: '../scss/_icons.scss'
        }))
        .pipe(iconfont({
            fontName: 'icons',
            formats: ['ttf', 'woff', 'woff2']
        }))
        .pipe(gulp.dest('./src/front/fonts'))
);

gulp.task(
    'assets-copy',
    () => gulp
        .src(
            [
                './src/admin/**/*',
                './src/front/**/*',
                '!./src/**/icons/**/*',
                '!./**/scss/',
                '!./**/*.scss',
                '!./**/*.map'
            ],
            {
                base: '.',
                nodir: true
            }
        )
        .pipe(rename((path) => {
            path.dirname = path.dirname.replace('src', 'assets');
            path.dirname = path.dirname.replace('scss', 'css');
        }))
        .pipe(gulp.dest('.'))
);

gulp.task(
    'scripts-build',
    () => gulp
        .src(
            [
                './assets/**/js/*.js',
                './assets/**/js/**/*.js',
                '!./assets/**/js/**/*.min.js'
            ],
            {base: '.'}
        )
        .pipe(babel({presets: [['@babel/preset-env', {modules: false}]]}))
        .pipe(gulp.dest('.'))
);

gulp.task(
    'scripts-compress',
    () => gulp
        .src(
            [
                './assets/**/js/*.js',
                './assets/**/js/**/*.js',
                '!./assets/**/js/**/*.min.js'
            ],
            {base: '.'}
        )
        .pipe(uglify({output: {comments: false}}))
        .pipe(rename((path) => {
            path.extname = '.min' + path.extname;
        }))
        .pipe(gulp.dest('.'))
);

gulp.task(
    'scripts-concat',
    () => gulp
        .src([
            './src/front/js/util.js',
            './src/front/js/modal.js',
            './src/front/js/wNumb.js',
            './src/front/js/nouislider.js',
            './src/front/js/nouislider-launcher.js',
            './src/front/js/sticky-kit.js',
            './src/front/js/masonry.pkgd.js',
            './src/front/js/wcpw.jquery.js',
            './src/front/js/wcpw-variation-form.jquery.js',
            './src/front/js/hooks.js'
        ])
        .pipe(concat('scripts.min.js'))
        .pipe(babel({presets: [['@babel/preset-env', {modules: false}]]}))
        .pipe(uglify({output: {comments: false}}))
        .pipe(gulp.dest('./assets/front/js/'))
);

gulp.task(
    'scripts-copy',
    () => gulp
        .src(
            [
                './src/admin/js/*',
                './src/front/js/*'
            ],
            {
                base: '.',
                nodir: true
            }
        )
        .pipe(rename((path) => {
            path.dirname = path.dirname.replace('src', 'assets');
        }))
        .pipe(gulp.dest('.'))
);

gulp.task(
    'scripts',
    gulp.series(
        'scripts-copy',
        'scripts-build',
        'scripts-compress',
        'scripts-concat'
    )
);

gulp.task('pot', () => new Promise((resolve) => {
    wpPot({
        domain: 'woocommerce-products-wizard',
        'package': 'WooCommerce Products Wizard',
        destFile: 'languages/woocommerce-products-wizard.pot',
        src: [
            './woocommerce-products-wizard.php',
            'views/**/*.php',
            'includes/**/*.php',
            '!includes/vendor/**/*.php'
        ],
        /* eslint-disable */
        gettextFunctions: [
            {name: 'L10N::r'},
            {name: 'L10N::e'},
            {name: '__'},
            {name: '_e'},
            {name: '_ex', context: 2},
            {name: '_n', plural: 2},
            {name: '_n_noop', plural: 2},
            {name: '_nx', plural: 2, context: 4},
            {name: '_nx_noop', plural: 2, context: 3},
            {name: '_x', context: 2},
            {name: 'esc_attr__'},
            {name: 'esc_attr_e'},
            {name: 'esc_attr_x', context: 2},
            {name: 'esc_html__'},
            {name: 'esc_html_e'},
            {name: 'esc_html_x', context: 2}
        ]
        /* eslint-enable */
    });

    resolve();
}));

gulp.task(
    'default',
    gulp.series(
        'pot',
        'icon-font',
        'styles-build',
        'assets-copy',
        'scripts-build',
        gulp.parallel([
            'scripts-compress',
            'scripts-concat',
            'styles-compress'
        ])
    )
);

gulp.task(
    'zip',
    () => gulp
        .src(
            [
                './*',
                './**/*',
                '!./node_modules',
                '!./node_modules/**/*',
                '!./package-lock.json',
                '!./woocommerce-products-wizard.zip'
            ],
            {base: '../'}
        )
        .pipe(zip('woocommerce-products-wizard.zip'))
        .pipe(gulp.dest('./'))
);
