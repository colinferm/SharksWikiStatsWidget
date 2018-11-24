<?php 

class StatsWidgetHelp {
        public static function editorHelp(EditPage &$editPage, OutputPage &$output) {
            $chartHelp = '<div class="statsWidgetHelp">';

            $chartHelp .= "<h3>Available Charts</h3>";

            //shark-season-investment help
            $chartHelp .= '<div class="statsWidgetHelpContainer">';
            $chartHelp .= '<div class="helpHeader"><h4>Season By Season Investment Totals (Line)</h4></div>';
            $chartHelp .= '<div class="helpBody">';
            $chartHelp .= '<img src="/extensions/StatsWidget/img/shark-season-investment.png"><br/>';
            $chartHelp .= '<code class="stats-demo">&lt;statschart type="shark-season-investment"/&gt;</code><br/>';
            $chartHelp .= 'Possible parameters:';
            $chartHelp .= '<ul>';
            $chartHelp .= '<li><code>start</code> - If set, will start the data range at the given season.</li>';
            $chartHelp .= '<li><code>end</code> - If set, end the data range at the given season.</li>';
            $chartHelp .= '<li><code>main-cast</code> - Will display only the main cast if <code>true</code> (default) or all sharks if <code>false</code>.</li>';
            $chartHelp .= '<li><code>shark</code> - If set, will add this shark to the displayed sharks (intended for non-main-cast sharks)</li>';
            $chartHelp .= '<li><code>category</code> - Only display data belonging the particular deal type category, if set.</li>';
            $chartHelp .= '<li><code>categories</code> - Only display data belonging the particular deal type categories, if set.</li>';
            $chartHelp .= '<li><code>title</code> - The title to set, overrides the default.</li>';
            $chartHelp .= '<li><code>style</code> - Either <code>size-full</code> or <code>size-half</code> [<code>right|left</code>].</li>';
            $chartHelp .= '</ul>';
            $chartHelp .= '<hr size="1"/>';
            $chartHelp .= '</div></div>';

            //categories-by-season help
            $chartHelp .= '<div class="statsWidgetHelpContainer">';
            $chartHelp .= '<div class="helpHeader"><h4>Categories By Season (Line)</h4></div>';
            $chartHelp .= '<div class="helpBody">';
            $chartHelp .= '<img src="/extensions/StatsWidget/img/categories-by-season.png"><br/>';
            $chartHelp .= '<code class="stats-demo">&lt;statschart type="scategories-by-season"/&gt;</code><br/>';
            $chartHelp .= 'Possible parameters:';
            $chartHelp .= '<ul>';
            $chartHelp .= '<li><code>start</code> - If set, will start the data range at the given season.</li>';
            $chartHelp .= '<li><code>end</code> - If set, end the data range at the given season.</li>';
            $chartHelp .= '<li><code>category</code> - Only display data belonging the particular deal type category, if set.</li>';
            $chartHelp .= '<li><code>categories</code> - Only display data belonging the particular deal type categories, if set.</li>';
            $chartHelp .= '<li><code>title</code> - The title to set, overrides the default.</li>';
            $chartHelp .= '<li><code>style</code> - Either <code>size-full</code> or <code>size-half</code> [<code>right|left</code>].</li>';
            $chartHelp .= '</ul>';
            $chartHelp .= '<hr size="1"/>';
            $chartHelp .= '</div></div>';

            //deal-type-mix help
            $chartHelp .= '<div class="statsWidgetHelpContainer">';
            $chartHelp .= '<div class="helpHeader"><h4>Deal Type Mix (Bar)</h4></div>';
            $chartHelp .= '<div class="helpBody">';
            $chartHelp .= '<img src="/extensions/StatsWidget/img/deal-type-mix.png"><br/>';
            $chartHelp .= '<code class="stats-demo">&lt;statschart type="deal-type-mix"/&gt;</code><br/>';
            $chartHelp .= 'Possible parameters:';
            $chartHelp .= '<ul>';
            $chartHelp .= '<li><code>season</code> - If set, will display data from a particular season, otherwise will total all seaons (default)</li>';
            $chartHelp .= '<li><code>main-cast</code> - Will display only the main cast if <code>true</code> (default) or all sharks if <code>false</code>.</li>';
            $chartHelp .= '<li><code>category</code> - Only display data belonging the particular deal type category, if set.</li>';
            $chartHelp .= '<li><code>categories</code> - Only display data belonging the particular deal type categories, if set.</li>';
            $chartHelp .= '<li><code>style</code> - Either <code>size-full</code> or <code>size-half</code> [<code>right|left</code>].</li>';
            $chartHelp .= '</ul>';
            $chartHelp .= '<hr size="1"/>';
            $chartHelp .= '</div></div>';

            //shark-rel-investment help
            $chartHelp .= '<div class="statsWidgetHelpContainer">';
            $chartHelp .= '<div class="helpHeader"><h4>Relative Shark Investments (Bubble)</h4></div>';
            $chartHelp .= '<div class="helpBody">';
            $chartHelp .= '<img src="/extensions/StatsWidget/img/shark-rel-investment.png"><br/>';
            $chartHelp .= '<code class="stats-demo">&lt;statschart type="shark-rel-investment"/&gt;</code><br/>';
            $chartHelp .= 'Possible parameters:';
            $chartHelp .= '<ul>';
            $chartHelp .= '<li><code>start</code> - If set, will start the data range at the given season.</li>';
            $chartHelp .= '<li><code>end</code> - If set, end the data range at the given season.</li>';
            $chartHelp .= '<li><code>main-cast</code> - Will display only the main cast if <code>true</code> (default) or all sharks if <code>false</code>.</li>';
            $chartHelp .= '<li><code>shark</code> - If set, will add this shark to the displayed sharks (intended for non-main-cast sharks)</li>';
            $chartHelp .= '<li><code>category</code> - Only display data belonging the particular deal type category, if set.</li>';
            $chartHelp .= '<li><code>categories</code> - Only display data belonging the particular deal type categories, if set.</li>';
            $chartHelp .= '<li><code>title</code> - The title to set, overrides the default.</li>';
            $chartHelp .= '<li><code>style</code> - Either <code>size-full</code> or <code>size-half</code> [<code>right|left</code>].</li>';
            $chartHelp .= '</ul>';
            $chartHelp .= '<hr size="1"/>';
            $chartHelp .= '</div></div>';

            //season-investment-by-category help
            $chartHelp .= '<div class="statsWidgetHelpContainer">';
            $chartHelp .= '<div class="helpHeader"><h4>Season Investments By Category (Pie)</h4></div>';
            $chartHelp .= '<div class="helpBody">';
            $chartHelp .= '<img src="/extensions/StatsWidget/img/season-investment-by-category.png" class="half"><br/>';
            $chartHelp .= '<code class="stats-demo">&lt;statschart type="season-investment-by-category"/&gt;</code><br/>';
            $chartHelp .= 'Possible parameters:';
            $chartHelp .= '<ul>';
            $chartHelp .= '<li><code>season</code> - If set, will limit the data to the given season, otherwill total all seasons.</li>';
            $chartHelp .= '<li><code>shark</code> - <b>Required</b> - Will display for this particular shark.</li>';
            $chartHelp .= '<li><code>limit</code> - Will limit the data to this number of categories. Will default to all.</li>';
            $chartHelp .= '<li><code>style</code> - Either <code>size-full</code> or <code>size-half</code> [<code>right|left</code>].</li>';
            $chartHelp .= '</ul>';
            $chartHelp .= '<hr size="1"/>';
            $chartHelp .= '</div></div>';

            //season-investment-by-type help
            $chartHelp .= '<div class="statsWidgetHelpContainer">';
            $chartHelp .= '<div class="helpHeader"><h4>Season Investments By Deal Type (Pie)</h4></div>';
            $chartHelp .= '<div class="helpBody">';
            $chartHelp .= '<img src="/extensions/StatsWidget/img/season-investment-by-type.png" class="half"><br/>';
            $chartHelp .= '<code class="stats-demo">&lt;statschart type="season-investment-by-type"/&gt;</code><br/>';
            $chartHelp .= 'Possible parameters:';
            $chartHelp .= '<ul>';
            $chartHelp .= '<li><code>season</code> - If set, will limit the data to the given season, otherwill total all seasons.</li>';
            $chartHelp .= '<li><code>shark</code> - If set, will limit data to this particular shark.</li>';
            $chartHelp .= '<li><code>limit</code> - Will limit the data to this number of categories. Will default to all.</li>';
            $chartHelp .= '<li><code>style</code> - Either <code>size-full</code> or <code>size-half</code> [<code>right|left</code>].</li>';
            $chartHelp .= '</ul>';
            $chartHelp .= '<hr size="1"/>';
            $chartHelp .= '</div></div>';

            //shark-amt-investment help
            $chartHelp .= '<div class="statsWidgetHelpContainer">';
            $chartHelp .= '<div class="helpHeader"><h4>Shark Investment Totals (Pie)</h4></div>';
            $chartHelp .= '<div class="helpBody">';
            $chartHelp .= '<img src="/extensions/StatsWidget/img/shark-amt-investment.png" class="half"><br/>';
            $chartHelp .= '<code class="stats-demo">&lt;statschart type="shark-amt-investment"/&gt;</code><br/>';
            $chartHelp .= 'Possible parameters:';
            $chartHelp .= '<ul>';
            $chartHelp .= '<li><code>season</code> - If set, will limit the data to the given season, otherwill total all seasons.</li>';
            $chartHelp .= '<li><code>main-cast</code> - Will display only the main cast if <code>true</code> (default) or all sharks if <code>false</code>.</li>';
            $chartHelp .= '<li><code>shark</code> - If set, will add the given shark to the data set (when <code>main-cast</code> is <code>true</code>.</li>';
            $chartHelp .= '<li><code>category</code> - Only display data belonging the particular deal type category, if set.</li>';
            $chartHelp .= '<li><code>categories</code> - Only display data belonging the particular deal type categories, if set.</li>';
            $chartHelp .= '<li><code>style</code> - Either <code>size-full</code> or <code>size-half</code> [<code>right|left</code>].</li>';
            $chartHelp .= '</ul>';
            $chartHelp .= '<hr size="1"/>';
            $chartHelp .= '</div></div>';

            //bite-by-season help
            $chartHelp .= '<div class="statsWidgetHelpContainer">';
            $chartHelp .= '<div class="helpHeader"><h4>Bite By Season (Line)</h4></div>';
            $chartHelp .= '<div class="helpBody">';
            $chartHelp .= '<img src="/extensions/StatsWidget/img/bite-by-season.png" class="half"><br/>';
            $chartHelp .= '<code class="stats-demo">&lt;statschart type="bite-by-season"/&gt;</code><br/>';
            $chartHelp .= 'Possible parameters:';
            $chartHelp .= '<ul>';
            $chartHelp .= '<li><code>start</code> - If set, will start the data range at the given season.</li>';
            $chartHelp .= '<li><code>end</code> - If set, end the data range at the given season.</li>';
            $chartHelp .= '<li><code>shark</code> - If set, will add the given shark to the data set (when <code>main-cast</code> is <code>true</code>.</li>';
            $chartHelp .= '<li><code>category</code> - Only display data belonging the particular deal type category, if set.</li>';
            $chartHelp .= '<li><code>categories</code> - Only display data belonging the particular deal type categories, if set.</li>';
            $chartHelp .= '<li><code>average</code> - Whether to average the amounts rather than total (Default <code>false</code>).</li>';
            $chartHelp .= '<li><code>style</code> - Either <code>size-full</code> or <code>size-half</code> [<code>right|left</code>].</li>';
            $chartHelp .= '</ul>';
            $chartHelp .= '<hr size="1"/>';
            $chartHelp .= '</div></div>';

            $chartHelp .= "</div>";

            $chartHelp .= '<script>
                RLQ.push(function() {
                    mw.debug = true
                    mw.loader.using(["ext.statsforsharks.statswidget.js"]).done(function() {
                        var $ = jQuery;
                        $(".helpBody").hide();
                        $(".helpHeader").click(function() {
                            $(this).siblings(".helpBody").toggle();
                        });
                    })
                });
            </script>
            ';

            $editPage->editFormTextAfterTools .= $chartHelp;
            return true;
        }
}
?>